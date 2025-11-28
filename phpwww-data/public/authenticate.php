<?php
// INICIA SESIÓN PRIMERO
session_start();

require_once '../app/pdo.php';
require_once '../app/auth.php';
require_once '../app/csrf.php';
require_once '../app/utils.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Validar CSRF
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die('Token CSRF inválido - Posible ataque detectado');
}
clear_csrf_token();

// Sanitizar inputs
$username = sanitize_text($_POST['username']);
$password = $_POST['password'];

// Validaciones básicas
if (empty($username) || empty($password)) {
    $_SESSION['old'] = ['username' => $username];
    redirect_with_message('login.php', 'Usuario y contraseña requeridos', true);
}

// Verificar en BD
$user = verify_credentials_db($username, $password);

if ($user) {
    // Login exitoso
    login_user($user['id'], $user['username']);
    unset($_SESSION['old']);
    
    // Redirigir a página previa o dashboard
    $redirect = $_SESSION['redirect_after_login'] ?? 'dashboard.php';
    unset($_SESSION['redirect_after_login']);
    header("Location: $redirect");
    exit;
} else {
    // Error: guardar username para "old()"
    $_SESSION['old'] = ['username' => $username];
    redirect_with_message('login.php', 'Credenciales incorrectas', true);
}
?>