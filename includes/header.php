<?php
require_once __DIR__ . '/auth.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Big Bar's</title>
    <link rel="stylesheet" href="/public/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="/public/index.php" class="brand">
        <img src="/public/assets/imgs/logo.svg" alt="Big Bar's" class="logo">
        <span>Big Bar's</span>
    </a>

    <a href="/public/carrinho.php">Carrinho</a>

    <?php if (isAdmin()): ?>
        <a href="/public/produtos/index.php">Gerenciar Produtos</a>
    <?php endif; ?>

    <?php if (!isAuthenticated()): ?>
        <a href="/public/login.php">Login</a>
    <?php else: ?>
        <form action="/public/logout.php" method="post" style="display:inline;">
            <button type="submit" class="btn-logout">
                Sair (<?= htmlspecialchars($_SESSION['user']['email']) ?>)
            </button>
        </form>
    <?php endif; ?>
</nav>

<main class="page-content">
