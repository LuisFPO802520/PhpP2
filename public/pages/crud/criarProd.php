<?php
require_once __DIR__ . '/../../../includes/database.php';
require_once __DIR__ . '/../../../includes/auth.php';

if (!isAdmin()) {
    header('Location: /public/index.php');
    exit;
}

$pdo = Database::getInstance()->getConnection();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = str_replace(',', '.', trim($_POST['preco'] ?? ''));
    $disponibilidade = isset($_POST['disponibilidade']) ? 1 : 0;

    if ($nome === '' || $preco === '') {
        $errors[] = "Nome e preço são obrigatórios.";
    }
    if (!is_numeric($preco)) {
        $errors[] = "Preço inválido.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO menu_items (nome, descricao, preco, disponibilidade, criado_em) VALUES (?, ?, ?, ?, datetime('now'))");
        $stmt->execute([$nome, $descricao, (float)$preco, $disponibilidade]);
        header('Location: /public/pages/crud/listarProd.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Adicionar Prato - Big Bar's</title></head>
<body>
<h2>Adicionar Prato</h2>
<p><a href="/public/pages/crud/listarProd.php">Voltar</a></p>

<?php if (!empty($errors)): ?>
    <?php foreach ($errors as $e): ?>
        <div style="color:red"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<form method="post" action="">
    <label>Nome: 
        <input type="text" name="nome" value="<?=htmlspecialchars($nome ?? '')?>">
    </label><br>
    <label>Descrição: 
        <textarea name="descricao"><?=htmlspecialchars($descricao ?? '')?></textarea>
    </label><br>
    <label>Preço (ex: 10.50): 
        <input type="text" name="preco" value="<?=htmlspecialchars($preco ?? '')?>">
    </label><br>
    <label>
        <input type="checkbox" name="disponibilidade" <?=(!isset($disponibilidade) || $disponibilidade) ? 'checked' : ''?>> Disponível
    </label><br>
    <button type="submit">Criar</button>
</form>
</body>
</html>
