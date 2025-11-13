<?php
require_once __DIR__ . '/../../../includes/auth.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $errors[] = "Preencha todos os campos.";
    } else {
        if (login($email, $senha)) {
            header('Location: /index.php');
            exit;
        } else {
            $errors[] = "E-mail ou senha inválidos.";
        }
    }
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Login - Big Bar's</title>
    <link rel="stylesheet" href="/assets/css/login.css">
</head>
<body>

<div class="login-container">
    
    <h1>Login - Big Bar's</h1>

    <?php include __DIR__ . '/../../../includes/messages.php'; ?>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $e): ?>
                <p><?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>


    <form method="post">

        <input type="email" name="email" placeholder="Seu e-mail" required>

        <input type="password" name="senha" placeholder="Sua senha" required>

        <button type="submit">Entrar</button>
    </form>

    <p>
        Ainda não tem conta?  
        <a href="/pages/account/register.php">Criar conta</a>
    </p>

</div>

</body>
</html>
