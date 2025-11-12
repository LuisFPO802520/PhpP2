<?php
require_once __DIR__ . '/../../../includes/database.php';
require_once __DIR__ . '/../../../includes/auth.php';

if (!isAdmin()) {
    header('Location: /public/index.php');
    exit;
}

$pdo = Database::getInstance()->getConnection();
$stmt = $pdo->query("SELECT * FROM menu_items ORDER BY criado_em DESC");
$items = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Menu - Big Bar's</title></head>
<body>
<h2>Menu - Big Bar's</h2>
<p><a href="/public/index.php">Voltar</a> | <a href="/public/pages/crud/criarProd.php">Adicionar Prato</a></p>

<table border="1" cellpadding="6">
    <thead><tr><th>Nome</th><th>Descrição</th><th>Preço</th><th>Disp.</th><th>Ações</th></tr></thead>
    <tbody>
    <?php if (!empty($items)): ?>
        <?php foreach ($items as $it): ?>
            <tr>
                <td><?=htmlspecialchars($it['nome'])?></td>
                <td><?=htmlspecialchars($it['descricao'])?></td>
                <td>R$ <?=number_format($it['preco'], 2, ',', '.')?></td>
                <td><?= $it['disponibilidade'] ? 'Sim' : 'Não' ?></td>
                <td>
                    <a href="/public/pages/crud/editarProd.php?id=<?= $it['id'] ?>">Editar</a> |
                    <a href="/public/pages/crud/deletarProd.php?id=<?= $it['id'] ?>">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="5">Nenhum prato cadastrado.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</body>
</html>
