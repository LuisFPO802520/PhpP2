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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: /public/pages/crud/listarProd.php');
    exit;
} else {
    $stmt = $pdo->prepare("SELECT nome FROM menu_items WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();
    if (!$item) {
        header('Location: /public/pages/crud/listarProd.php');
        exit;
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Excluir - Big Bar's</title></head>
<body>
<h2>Excluir Prato</h2>
<p>Tem certeza que deseja excluir: <strong><?=htmlspecialchars($item['nome'])?></strong> ?</p>
<form method="post" action="">
    <button type="submit">Sim, excluir</button>
    <a href="/public/pages/crud/listarProd.php">Cancelar</a>
</form>
</body>
</html>
