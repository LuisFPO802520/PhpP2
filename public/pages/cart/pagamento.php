<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../../includes/header.php';
$checkout = require_once __DIR__ . '/../../../includes/checkout.php';
extract($checkout);

echo '<link rel="stylesheet" href="/assets/css/checkout.css">';

if (empty($cart)) {
    echo "<div class='pagamento-wrap fade-in'><h2>Pagamento</h2><p>Seu carrinho está vazio. <a href='/index.php'>Voltar</a></p></div>";
    exit;
}
?>
<button class="btn-voltar">
    <a href="carrinho.php">⬅ Voltar</a>
</button>


<div class="pagamento-wrap fade-in">
    <h2>Checkout</h2>

    <?php if ($popup): ?>
        <div class="popup-overlay" id="popup">
            <div class="popup fade-slide-up">
                <h2>✅ Compra finalizada!</h2>
                <p>Forma de pagamento: <strong><?= htmlspecialchars($metodo) ?></strong></p>
                <p>Obrigado pela preferência 💛</p>
                <a href="/index.php"><button class="popup-ok-btn">OK</button></a>
            </div>
        </div>

        <script>
            setTimeout(() => {
                const popup = document.getElementById('popup');
                if (popup) {
                    popup.style.opacity = '0';
                    popup.style.transition = 'opacity 0.6s ease';
                    setTimeout(() => popup.remove(), 600);
                }
            }, 3500);
        </script>
    <?php endif; ?>

    <?php if (!empty($erro)): ?>
        <p style="color:red;text-align:center;"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <div class="pagamento-grid">
        <form method="post" action="" class="form-pagamento">
            <label>Nome Completo</label>
            <input type="text" name="nome" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Endereço</label>
            <input type="text" name="endereco" required>

            <label>Método de Pagamento</label>
            <div class="metodos">
                <button type="button" class="metodo" onclick="selectMetodo('Cartão')">Cartão</button>
                <button type="button" class="metodo" onclick="selectMetodo('Pix')">Pix</button>
                <button type="button" class="metodo" onclick="selectMetodo('Boleto')">Boleto</button>
                <button type="button" class="metodo" onclick="selectMetodo('Dinheiro Físico')">Dinheiro Físico</button>
            </div>
            <input type="hidden" name="metodo_pagamento" id="metodo_pagamento" value="<?= htmlspecialchars($metodo) ?>">

            <div id="extra-fields"></div>

            <label>Cupom de desconto</label>
            <div class="field-inline">
                <input type="text" name="cupom" placeholder="Ex: DESCONTO10" value="<?= htmlspecialchars($cupom) ?>">
                <button type="submit" class="cupom-btn">Aplicar</button>
            </div>

            <button type="submit" class="pagar-btn">Finalizar Compra</button>
        </form>

        <div class="resumo-pedido fade-slide-up">
            <h3>Resumo</h3>
            <?php foreach ($cart as $item): ?>
                <div class="linha">
                    <span><?= htmlspecialchars($item['nome']) ?> (x<?= $item['quantidade'] ?>)</span>
                    <span>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></span>
                </div>
            <?php endforeach; ?>

            <?php if ($desconto > 0): ?>
                <div class="linha desconto">
                    <span>Desconto:</span>
                    <span>- R$ <?= number_format($desconto, 2, ',', '.') ?></span>
                </div>
            <?php endif; ?>

            <div class="total">
                Total: R$ <?= number_format($totalFinal, 2, ',', '.') ?>
            </div>
        </div>
    </div>
</div>

<script>
    const metodoInput = document.getElementById('metodo_pagamento');
    const extraFields = document.getElementById('extra-fields');

    function selectMetodo(metodo) {
        metodoInput.value = metodo;

        document.querySelectorAll('.metodo').forEach(btn => btn.classList.remove('selected'));
        document.querySelectorAll('.metodo').forEach(btn => {
            if (btn.textContent.trim() === metodo) btn.classList.add('selected');
        });

        extraFields.innerHTML = '';

        if (metodo === 'Cartão') {
            extraFields.innerHTML = `
                <label>Número do Cartão</label>
                <input type="text" name="numero_cartao" placeholder="0000 0000 0000 0000">
                <div class="field-inline">
                    <div>
                        <label>Validade</label>
                        <input type="text" name="validade" placeholder="MM/AA">
                    </div>
                    <div>
                        <label>CVV</label>
                        <input type="text" name="cvv" placeholder="000">
                    </div>
                </div>`;
        } 
        else if (metodo === 'Pix') {
            extraFields.innerHTML = `
                <label>Código PIX</label>
                <input type="text" name="codigo_pix" placeholder="Ex: a1b2-c3d4-e5f6-g7h8">`;
        } 
        else if (metodo === 'Boleto') {
            extraFields.innerHTML = `
                <label>Código de Boleto</label>
                <input type="text" name="codigo_boleto" placeholder="Ex: 34191.79001 01043.510047 91020.150008 8 72590026000">`;
        } 
        else if (metodo === 'Dinheiro Físico') {
            extraFields.innerHTML = `
                <label>Código do Atendente</label>
                <input type="text" name="codigo_atendente" placeholder="Ex: 48-B">`;
        }
    }
</script>
<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
