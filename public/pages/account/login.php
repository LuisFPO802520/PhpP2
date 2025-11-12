<?php
require_once __DIR__ . '/../../../includes/database.php';
require_once __DIR__ . '/../../../includes/auth.php';

$pdo = Database::getInstance()->getConnection();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $errors[] = "Preencha todos os campos.";
    } else {
        $stmt = $pdo->prepare("SELECT id, nome, email, senha, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha'])) {
            // Usa o novo sistema de sessão do auth.php
            login($user['email'], $user['role']);
            $_SESSION['user']['id'] = $user['id'];
            $_SESSION['user']['nome'] = $user['nome'];
            header('Location: /public/index.php');
            exit;
        } else {
            $errors[] = "Email ou senha incorretos.";
        }
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>Login - Big Bar's</title>
</head>
<body>
<h2>Login - Big Bar's</h2>

<?php if (!empty($errors)): ?>
    <?php foreach ($errors as $e): ?>
        <div style="color:red"><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>
<?php endif; ?>

<form method="post">
    <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>"></label><br>
    <label>Senha: <input type="password" name="senha"></label><br>
    <button type="submit">Entrar</button>
</form>
<p><a href="/public/pages/account/register.php">Criar conta</a></p>
</body>
</html>
