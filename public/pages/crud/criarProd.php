<?php
require_once __DIR__ . '/../../../includes/database.php';
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../includes/header.php';

if (!isAdmin()) {
    header('Location: /index.php');
    exit;
}

$pdo = Database::getInstance()->getConnection();
$errors = [];
$nome = $descricao = $preco = $imagem = '';
$quantidade = 0;
$disponibilidade = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = str_replace(',', '.', trim($_POST['preco'] ?? ''));
    $imagem = trim($_POST['imagem'] ?? '');
    $quantidade = (int)($_POST['quantidade'] ?? 0);
    $disponibilidade = isset($_POST['disponibilidade']) ? 1 : 0;

    if ($nome === '' || $preco === '') {
        $errors[] = "Nome e preço são obrigatórios.";
    }
    if ($preco !== '' && !is_numeric($preco)) {
        $errors[] = "Preço inválido.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO menu_items (nome, descricao, preco, quantidade, disponibilidade, imagem, criado_em)
            VALUES (?, ?, ?, ?, ?, ?, datetime('now'))
        ");

        if ($stmt->execute([$nome, $descricao, (float)$preco, $quantidade, $disponibilidade, $imagem])) {
            header('Location: /pages/crud/listarProd.php?success=Produto criado com sucesso!');
        } else {
            header('Location: /pages/crud/listarProd.php?error=Erro ao criar o produto.');
        }
        exit;
    }
}
?>

<link rel="stylesheet" href="/assets/css/crud.css">

<div class="dashboard-layout">
    <?php include __DIR__ . '/../../../includes/painel_admin.php'; ?>

    <main class="dashboard-content">
        <div class="titulo-crud">
            <h2>Adicionar Produto</h2>
        </div>

        <div class="criar-produto-container">
            <?php include __DIR__ . '/../../../includes/messages.php'; ?>

            <?php if (!empty($errors)): ?>
                <?php foreach ($errors as $e): ?>
                    <div class="erro-criar-prod"><?= htmlspecialchars($e) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <form method="post" action="" class="form-criar-produto" id="form-criar-produto">
                <label>Nome do produto</label>
                <input type="text" name="nome" class="input-criar-produto" value="<?= htmlspecialchars($nome) ?>" required>

                <label>Descrição</label>
                <textarea name="descricao" class="input-criar-produto" rows="4"><?= htmlspecialchars($descricao) ?></textarea>

                <label>Preço (ex: 1.99)</label>
                <input type="text" name="preco" class="input-criar-produto" value="<?= htmlspecialchars($preco) ?>" required>

                <label>Quantidade inicial</label>
                <input type="number" name="quantidade" class="input-criar-produto" value="<?= htmlspecialchars($quantidade) ?>">

                <label>URL da imagem (opcional)</label>
                <input 
                    type="url" 
                    name="imagem" 
                    id="imagem-url" 
                    class="input-criar-produto" 
                    placeholder="https://exemplo.com/imagem.jpg"
                    value="<?= htmlspecialchars($imagem) ?>"
                >

                <div id="preview-container" class="preview-container ocultar-preview">
                    <p>Pré-visualização da imagem:</p>
                    <img id="preview-imagem" class="preview-imagem" src="" alt="Pré-visualização">
                </div>

                <label class="checkbox-disponibilidade">
                    <input type="checkbox" name="disponibilidade" <?= $disponibilidade ? 'checked' : '' ?>>
                    <span>Disponível no cardápio</span>
                </label>

                <button type="submit" class="btn-criar-produto">Criar Produto</button>
            </form>
        </div>
    </main>
</div>

<script>
const imagemInput = document.getElementById('imagem-url');
const previewContainer = document.getElementById('preview-container');
const previewImg = document.getElementById('preview-imagem');

imagemInput.addEventListener('input', () => {
    const url = imagemInput.value.trim();
    if (url) {
        previewImg.src = url;
        previewContainer.classList.remove('ocultar-preview');
    } else {
        previewContainer.classList.add('ocultar-preview');
    }
});

previewImg.addEventListener('error', () => {
    previewContainer.classList.add('ocultar-preview');
});
</script>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
