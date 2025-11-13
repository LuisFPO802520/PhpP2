<?php
require_once __DIR__ . '/database.php';

$db = Database::getInstance()->getConnection();
$stmt = $db->query("SELECT * FROM menu_items WHERE disponibilidade = 1 ORDER BY criado_em DESC");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="produtos">
    <?php if (empty($produtos)): ?>
        <p>Nenhum produto disponível no momento.</p>
    <?php else: ?>
        <?php foreach ($produtos as $p): ?>
            <div class="produto">
                <?php 
                    $imagem = !empty($p['imagem']) ? htmlspecialchars($p['imagem']) : '/assets/img/placeholder.png';
                ?>
                <img src="<?= $imagem ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
                
                <h3><?= htmlspecialchars($p['nome']) ?></h3>
                <p>R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                
                <?php if (!empty($p['descricao'])): ?>
                    <p class="text-muted"><?= htmlspecialchars($p['descricao']) ?></p>
                <?php endif; ?>

                <a href="/pages/cart/carrinho.php?action=add&id=<?= $p['id'] ?>">
                    <button>Comprar</button>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
