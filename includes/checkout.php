<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/cart.php';

$cart = getCart();
$total = getCartTotal();

$metodo = $_POST['metodo_pagamento'] ?? '';
$cupom = $_POST['cupom'] ?? '';
$desconto = 0;
$popup = false;
$erro = null;

// ⚠️ AQUI — Só verifica login quando realmente clicar em "Finalizar compra"
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // se não estiver logado → exibe erro mas NÃO redireciona
    if (!isAuthenticated()) {
        $erro = "Você precisa estar logado para finalizar a compra.";
    } else {

        if ($cupom && strtolower(trim($cupom)) === 'desconto10') {
            $desconto = $total * 0.10;
        }

        if (!$metodo) {
            $erro = "Selecione uma forma de pagamento.";
        } else {
            clearCart();
            $popup = true;
        }
    }
}

$totalFinal = $total - $desconto;

return [
    'cart' => $cart,
    'total' => $total,
    'desconto' => $desconto,
    'totalFinal' => $totalFinal,
    'popup' => $popup,
    'erro' => $erro,
    'metodo' => $metodo,
    'cupom' => $cupom,
];

/*
tem que validar os dados dessas formas de pagamento, mas vou deixar sem por enquanto
e talvez eu faça uma versao do checkout.php que retorna os dados em json pra usar com ajax, tipo uma api
ai o front ia atualizar o total em tempo real sem recarregar a página, mas sla muito trampo, escrevi pra lembrar caso esqueça
*/