<?php
// =====  PROTECCIÓN CSRF ====== //

// Genera un token CSRF y lo almacena en la sesión
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
// Verifica si el token CSRF proporcionado coincide con el almacenado en la sesión
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
 // Limpia el token CSRF de la sesión
function clear_csrf_token() {
    unset($_SESSION['csrf_token']);
}
?>