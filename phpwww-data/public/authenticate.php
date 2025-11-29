<?php
session_start();
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/csrf.php';
require_once __DIR__ . '/../app/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die('Token CSRF inválido');
}
clear_csrf_token();

$username = sanitize_text($_POST['username']);
$password = $_POST['password'];

if (empty($username) || empty($password)) {
    $_SESSION['old'] = ['username' => $username];
    redirect_with_message('login.php', 'Usuario y contraseña requeridos', true);
}

$user = verify_credentials_db($username, $password);

if ($user) {
    login_user($user['id'], $user['username']);
    unset($_SESSION['old']);
    
   
    $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
    unset($_SESSION['redirect_after_login']);
    header("Location: $redirect");
    exit;
} else {
    $_SESSION['old'] = ['username' => $username];
    redirect_with_message('login.php', 'Credenciales incorrectas', true);
}