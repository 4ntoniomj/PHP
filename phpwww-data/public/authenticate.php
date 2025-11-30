<?php
session_start();
require_once '../app/pdo.php';
require_once '../app/auth.php';
require_once '../app/csrf.php';
require_once '../app/utils.php';
// Procesa el formulario de inicio de sesión
// Solo procesar si es POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}
// Verifica el token CSRF
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die('Token CSRF inválido');
}
clear_csrf_token();
// Sanaea el username (previene XSS, inyección de codigo malicioso)
$username = sanitize_text($_POST['username']);
$password = $_POST['password'];

if (empty($username) || empty($password)) {
    $_SESSION['old'] = ['username' => $username];
    redirect_with_message('login.php', 'Usuario y contraseña requeridos', true);
}
// Verifica las credenciales
$user = verify_credentials_db($username, $password);
// Si son válidas, inicia sesión y redirige
if ($user) {
    login_user($user['id'], $user['username']);
    unset($_SESSION['old']);
    
   
    $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
    unset($_SESSION['redirect_after_login']);
    header("Location: $redirect");
    exit;
    // Si no, redirige con mensaje de error
} else {
    $_SESSION['old'] = ['username' => $username];
    redirect_with_message('login.php', 'Credenciales incorrectas', true);
}