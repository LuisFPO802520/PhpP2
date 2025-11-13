<?php require_once __DIR__ . '/../includes/header.php'; ?>
<?php include __DIR__ . '/../includes/messages.php'; ?>

<main>
    <section class="hero">
        <div>
            <img src="/assets/imgs/logo.png" alt="Logo Big Bar's" class="img-logo">
        </div>
        <h1>Bem-vindo ao <span style="color: var(--orange)">Big Bar's</span>!</h1>
        <p>Escolha seus produtos favoritos e aproveite as melhores ofertas.</p>
        <a href="#produtos">
            <button class="hero-btn">Explorar Produtos</button>
        </a>
    </section>

    <section id="produtos">
        <?php require_once __DIR__ . '/../includes/list_prod.php'; ?>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
