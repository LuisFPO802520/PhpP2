<?php
require_once __DIR__ . '/../../../includes/database.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/header.php';

if (!isAdmin()) {
    header('Location: /index.php');
    exit;
}

$pdo = Database::getInstance()->getConnection();

$stmt = $pdo->query("SELECT id, nome FROM menu_items ORDER BY nome ASC");
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$id = intval($_GET['id'] ?? 0);
$item = null;
$errors = [];

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if (!$item) {
        header('Location: /pages/crud/listarProd.php?error=Produto não encontrado.');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_id'])) {
    $id = intval($_POST['editar_id']);
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = str_replace(',', '.', trim($_POST['preco'] ?? ''));
    $quantidade = (int)($_POST['quantidade'] ?? 0);
    $imagem = trim($_POST['imagem'] ?? '');
    $disponibilidade = isset($_POST['disponibilidade']) ? 1 : 0;

    if ($nome === '' || $preco === '') $errors[] = "Nome e preço são obrigatórios.";
    if (!is_numeric($preco)) $errors[] = "Preço inválido.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            UPDATE menu_items
            SET nome = ?, descricao = ?, preco = ?, quantidade = ?, imagem = ?, disponibilidade = ?
            WHERE id = ?
        ");
        if ($stmt->execute([$nome, $descricao, (float)$preco, $quantidade, $imagem, $disponibilidade, $id])) {
            header('Location: /pages/crud/listarProd.php?success=Produto atualizado com sucesso!');
            exit;
        } else {
            $errors[] = "Erro ao atualizar o produto.";
        }
    }

    // Recarregar o item após atualização
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
}
?>

<link rel="stylesheet" href="/assets/css/crud.css">

<div class="dashboard-layout">
    <?php include __DIR__ . '/../../../includes/painel_admin.php'; ?>

    <main class="dashboard-content">
        <div class="titulo-crud">
            <h2>Editar Produto</h2>
        </div>

        <div class="criar-produto-container">
            <?php include __DIR__ . '/../../../includes/messages.php'; ?>

            <?php if (empty($item)): ?>
                <form method="get" class="form-criar-produto">
                    <label for="id">Selecione um produto:</label>
                    <select name="id" id="id" class="input-criar-produto" required>
                        <option value="">Escolher...</option>
                        <?php foreach ($produtos as $p): ?>
                            <option value="<?= $p['id'] ?>">#<?= $p['id'] ?> — <?= htmlspecialchars($p['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn-criar-produto"> Editar</button>
                </form>

            <?php else: ?>
                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $e): ?>
                        <div class="erro-criar-prod"><?= htmlspecialchars($e) ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <form method="post" class="form-criar-produto">
                    <input type="hidden" name="editar_id" value="<?= $item['id'] ?>">

                    <label>Nome do produto</label>
                    <input type="text" name="nome" class="input-criar-produto" value="<?= htmlspecialchars($item['nome']) ?>" required>

                    <label>Descrição</label>
                    <textarea name="descricao" class="input-criar-produto" rows="4"><?= htmlspecialchars($item['descricao']) ?></textarea>

                    <label>Preço (ex: 10.50)</label>
                    <input type="text" name="preco" class="input-criar-produto" value="<?= htmlspecialchars($item['preco']) ?>" required>

                    <label>Quantidade em estoque</label>
                    <input type="number" name="quantidade" class="input-criar-produto" value="<?= htmlspecialchars($item['quantidade'] ?? 0) ?>">

                    <label>URL da imagem</label>
                    <input type="url" name="imagem" id="imagem-url" class="input-criar-produto"
                           value="<?= htmlspecialchars($item['imagem'] ?? '') ?>"
                           placeholder="https://exemplo.com/imagem.jpg">

                    <div id="preview-container" class="preview-container <?= $item['imagem'] ? '' : 'ocultar-preview' ?>">
                        <p>Pré-visualização:</p>
                        <img id="preview-imagem" class="preview-imagem"
                             src="<?= htmlspecialchars($item['imagem']) ?>"
                             alt="Prévia"
                             onerror="this.parentElement.style.display='none'">
                    </div>

                    <label class="checkbox-disponibilidade">
                        <input type="checkbox" name="disponibilidade" <?= $item['disponibilidade'] ? 'checked' : '' ?>>
                        <span>Disponível no cardápio</span>
                    </label>

                    <button type="submit" class="btn-criar-produto"> Salvar Alterações</button>
                </form>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
const imagemInput = document.getElementById('imagem-url');
const previewContainer = document.getElementById('preview-container');
const previewImg = document.getElementById('preview-imagem');

if (imagemInput) {
    imagemInput.addEventListener('input', () => {
        const url = imagemInput.value.trim();
        if (url) {
            previewImg.src = url;
            previewContainer.classList.remove('ocultar-preview');
        } else {
            previewContainer.classList.add('ocultar-preview');
        }
    });
}
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
