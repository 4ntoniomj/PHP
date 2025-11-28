<?php
// =====  Protección CSRF para formularios ====== //
 
// Nos genera y almacena el token CSRF

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

 // Se verifica que el token anteriormente generado es igual que a la que se pasa a la función

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Se elimina el toquen creado en la sesion
 
function clear_csrf_token() {
    unset($_SESSION['csrf_token']);
}
?>