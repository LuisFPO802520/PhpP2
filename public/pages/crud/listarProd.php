<?php
require_once __DIR__ . '/../../../includes/database.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/header.php';

if (!isAdmin()) {
    header('Location: /index.php');
    exit;
}

$pdo = Database::getInstance()->getConnection();
$stmt = $pdo->query("SELECT * FROM menu_items ORDER BY criado_em DESC");
$items = $stmt->fetchAll();
?>

<link rel="stylesheet" href="/assets/css/crud.css">

<div class="dashboard-layout">
    <?php include __DIR__ . '/../../../includes/painel_admin.php'; ?>

    <div class="dashboard-content">
        <div class="titulo-crud">
            <h2>Gerenciar Produtos</h2>
        </div>

        <?php include __DIR__ . '/../../../includes/messages.php'; ?>

        <div class="botoes-crud">
            <a href="/pages/crud/criarProd.php">    + Adicionar Produto</a>
        </div>

        <!-- LISTA DE PRODUTOS (usando o mesmo estilo da Home) -->
        <div id="produtos" class="produtos fade-in">
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $produto): ?>
                    <div class="produto" key="<?= $produto['id'] ?>">
                        
                        <p><strong>ID:</strong> <?= htmlspecialchars($produto['id']) ?></p>
                        <?php if (!empty($produto['imagem'])): ?>
                            <img src="<?= htmlspecialchars($produto['imagem']) ?>" 
                                 alt="<?= htmlspecialchars($produto['nome']) ?>"
                                 onerror="this.style.display='none'">
                        <?php endif; ?>

                        <h3><?= htmlspecialchars($produto['nome']) ?></h3>
                        <p>R$ <?= number_format($produto['preco'], 2, ',', '.') ?></p>
                        <p>Quantidade: <?= $produto['quantidade'] ?? 0 ?></p>
                        <p>Status: <?= $produto['disponibilidade'] ? 'Disponível' : 'Indisponível' ?></p>

                        <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 12px;">
                            <form action="/pages/crud/editarProd.php" method="get">
                                <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                                <button type="submit" class="btn-criar-produto" style="padding:10px 0;">Editar</button>
                            </form>

                            <form action="/pages/crud/deletarProd.php" method="get">
                                <input type="hidden" name="id" value="<?= $produto['id'] ?>">
                                <button type="submit"
                                        class="btn-criar-produto"
                                        style="background: linear-gradient(90deg, #ff4646, #d32f2f);
                                               box-shadow: 0 0 14px rgba(255,70,70,0.35);
                                               padding:10px 0;">
                                    Excluir
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; margin-top:30px;">Nenhum produto cadastrado.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
