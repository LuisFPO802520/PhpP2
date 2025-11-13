<?php
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Big Bar's</title>
    <link rel="stylesheet" href="/assets/css/index.css">
</head>
<body>

<nav class="navbar">
    <a href="/index.php" class="brand">
        <img src="/assets/imgs/logo.svg" alt="Big Bar's" class="logo">
        <span>Big Bar's</span>
    </a>

    <a href="/pages/cart/carrinho.php">Carrinho</a>

    <?php if (isAdmin()): ?>
        <a href="/pages/crud/listarProd.php">Gerenciar Produtos</a>
    <?php endif; ?>

    <?php if (!isAuthenticated()): ?>
        <a href="/pages/account/login.php">Login</a>
    <?php else: ?>
        <form action="/pages/account/logout.php" method="post" style="display:inline;">
            <button type="submit" class="btn-logout">
                Sair (<?= htmlspecialchars($_SESSION['user']['email'] ?? 'Usuário') ?>)
            </button>
        </form>
    <?php endif; ?>
</nav>

<main class="page-content">
