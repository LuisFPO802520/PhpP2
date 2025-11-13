<?php
require_once __DIR__ . '/../../../includes/header.php';
require_once __DIR__ . '/../../../includes/cart.php';
require_once __DIR__ . '/../../../includes/database.php';

$pdo = Database::getInstance()->getConnection();

// Adicionar produto
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT id, nome, preco, imagem FROM menu_items WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        addToCart($product['id'], $product['nome'], $product['preco'], $product['imagem']);
    }

    header('Location: carrinho.php');
    exit;
}

// Remover produto
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    removeFromCart((int)$_GET['id']);
    header('Location: carrinho.php');
    exit;
}

// Somar ou subtrair quantidade
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'increase') increaseQuantity($id);
    if ($_GET['action'] === 'decrease') decreaseQuantity($id);
    header('Location: carrinho.php');
    exit;
}

// Esvaziar carrinho
if (isset($_GET['action']) && $_GET['action'] === 'clear') {
    clearCart();
    header('Location: carrinho.php');
    exit;
}

$cart = getCart();
$total = getCartTotal();
?>

<?php include __DIR__ . '/../../../includes/messages.php'; ?>
<link rel="stylesheet" href="/assets/css/cart.css">

<button class="btn-voltar">
    <a href="/index.php">⬅ Voltar</a>
</button>

<div class="pagina pagina-carrinho">
    <h2> Seu Carrinho</h2>

    <?php if (empty($cart)): ?>
        <p>Seu carrinho está vazio.</p>

    <?php else: ?>
        <ul>
            <?php foreach ($cart as $item): ?>
                <?php 
                    $img = !empty($item['imagem']) ? $item['imagem'] : "/../../assets/imgs/nada.png";
                ?>

                <li>
                    <img src="<?= htmlspecialchars($img) ?>" class="img-carrinho" alt="Imagem do produto">
                    <div class="gap-img-text">
                        <strong><?= htmlspecialchars($item['nome']) ?></strong><br>
                        R$ <?= number_format($item['preco'], 2, ',', '.') ?>
                    </div>

                    <div>
                        Quantidade:
                        <?= $item['quantidade'] ?> <br>
                        <a href="?action=decrease&id=<?= $item['id'] ?>" class="quantidade-btn">-</a>
                        <a href="?action=increase&id=<?= $item['id'] ?>" class="quantidade-btn">+</a>
                    </div>

                    <div>
                        Subtotal:<br>
                        R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?><br>
                        <a href="?action=remove&id=<?= $item['id'] ?>" class="btn-remover">Remover</a>

                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <h3>Total: R$ <?= number_format($total, 2, ',', '.') ?></h3>

        <div class="carrinho-actions">
            <?php if (!isAdmin()): ?>
                <a href="/pages/account/login.php" class="btn-padrao btn-pagamento">Ir para Pagamento</a>
            <?php else: ?>
                <a href="pagamento.php" class="btn-padrao btn-pagamento">Ir para Pagamento</a>
            <?php endif; ?>

            <a href="?action=clear" class="btn-padrao"
               onclick="return confirm('Esvaziar carrinho?')">Esvaziar Carrinho</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
