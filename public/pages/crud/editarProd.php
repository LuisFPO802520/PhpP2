<?php
require_once __DIR__ . '/../../../includes/database.php';
require_once __DIR__ . '/../../../includes/auth.php';

if (!isAdmin()) {
    header('Location: /public/index.php');
    exit;
}

$pdo = Database::getInstance()->getConnection();

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: /public/pages/crud/listarProd.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM menu_items WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    header('Location: /public/pages/crud/listarProd.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $preco = str_replace(',', '.', trim($_POST['preco'] ?? ''));
    $disponibilidade = isset($_POST['disponibilidade']) ? 1 : 0;

    if ($nome === '' || $preco === '') $errors[] = "Nome e preço obrigatórios.";
    if (!is_numeric($preco)) $errors[] = "Preço inválido.";

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE menu_items SET nome=?, descricao=?, preco=?, disponibilidade=? WHERE id=?");
        $stmt->execute([$nome, $descricao, (float)$preco, $disponibilidade, $id]);
        header('Location: /public/pages/crud/listarProd.php');
        exit;
    }
} else {
    $nome = $item['nome'];
    $descricao = $item['descricao'];
    $preco = $item['preco'];
    $disponibilidade = $item['disponibilidade'];
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Editar Prato - Big Bar's</title></head>
<body>
<h2>Editar Prato</h2>
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
    <label>Preço: 
        <input type="text" name="preco" value="<?=htmlspecialchars($preco ?? '')?>">
    </label><br>
    <label>
        <input type="checkbox" name="disponibilidade" <?= $disponibilidade ? 'checked' : ''?>> Disponível
    </label><br>
    <button type="submit">Salvar</button>
</form>
</body>
</html>
