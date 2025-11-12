<?php
require_once __DIR__ . '/database.php';
session_start();

function isAuthenticated() {
    return isset($_SESSION['user']);
}

function isAdmin() {
    return isAuthenticated() && $_SESSION['user']['role'] === 'admin';
}

function login($email, $senha) {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['user'] = [
            'id' => $user['id'],
            'nome' => $user['nome'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        return true;
    }

    return false;
}

function logout() {
    session_destroy();
    header("Location: /public/pages/account/login.php");
    exit;
}

function require_login() {
    if (!isAuthenticated()) {
        header("Location: /public/pages/account/login.php");
        exit;
    }
}

function require_admin() {
    require_login();
    if (!isAdmin()) {
        http_response_code(403);
        echo "<h1>Acesso negado</h1><p>Esta área é restrita a administradores.</p>";
        exit;
    }
}
?>
