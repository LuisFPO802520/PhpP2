<?php
require_once __DIR__ . '/../../../includes/database.php';
require_once __DIR__ . '/../../../includes/auth.php';

$pdo = Database::getInstance()->getConnection();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $senha_confirm = $_POST['senha_confirm'] ?? '';

    if ($nome === '' || $email === '' || $senha === '' || $senha_confirm === '') {
        $errors[] = "Preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "E-mail inválido.";
    } elseif ($senha !== $senha_confirm) {
        $errors[] = "As senhas não conferem.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "E-mail já cadastrado.";
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (nome, email, senha, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$nome, $email, $hash]);
            header('Location: /pages/account/login.php?registered=1');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Cadastro - Big Bar's</title>

    <!-- ATIVANDO O CSS DO LOGIN -->
    <link rel="stylesheet" href="/assets/css/login.css">
</head>

<body>

<div class="login-container">

    <h1> Criar Conta </h1>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $e): ?>
                <p><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">

        <input type="text" name="nome"
               placeholder="Seu nome"
               value="<?= htmlspecialchars($nome ?? '') ?>">

        <input type="email" name="email"
               placeholder="Seu e-mail"
               value="<?= htmlspecialchars($email ?? '') ?>">

        <input type="password" name="senha" placeholder="Senha">

        <input type="password" name="senha_confirm" placeholder="Confirmar senha">

        <button type="submit">Cadastrar</button>
    </form>

    <p>
        Já tem conta?
        <a href="/pages/account/login.php">Entrar</a>
    </p>
</div>

</body>
</html>
