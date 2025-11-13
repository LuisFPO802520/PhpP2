<?php
require_once __DIR__ . '/database.php';
session_start();

function isAuthenticated(): bool {
    return isset($_SESSION['user']);
}

function isAdmin(): bool {
    return isAuthenticated() && ($_SESSION['user']['role'] ?? '') === 'admin';
}

function login(string $email, string $senha): bool {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['senha'])) {
        $role = $user['role'] ?? 'user';

        $_SESSION['user'] = [
            'id'    => $user['id'],
            'nome'  => $user['nome'],
            'email' => $user['email'],
            'role'  => $role
        ];
        return true;
    }

    return false;
}

function logout(): void {
    session_destroy();
    header("Location: /index.php");
    exit;
}

function require_login(): void {
    if (!isAuthenticated()) {
        header("Location: /pages/account/login.php");
        exit;
    }
}

function require_admin(): void {
    require_login();
    if (!isAdmin()) {
        http_response_code(403);
        echo "<h1>Acesso negado</h1><p>Esta área é restrita a administradores.</p>";
        exit;
    }
}
?>
