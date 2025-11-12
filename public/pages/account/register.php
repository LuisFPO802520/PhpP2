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
            header('Location: /public/pages/account/login.php?registered=1');
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
</head>
<body>
<h2>Cadastro - Big Bar's</h2>

<?php if (!empty($errors)): ?>
    <div style="color:red">
        <?php foreach ($errors as $e): ?>
            <p><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post">
    <label>Nome: <input type="text" name="nome" value="<?= htmlspecialchars($nome ?? '') ?>"></label><br>
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>"></label><br>
    <label>Senha: <input type="password" name="senha"></label><br>
    <label>Confirmar senha: <input type="password" name="senha_confirm"></label><br>
    <button type="submit">Cadastrar</button>
</form>
<p><a href="/public/pages/account/login.php">Já tenho conta — Entrar</a></p>
</body>
</html>
