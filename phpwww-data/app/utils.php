<?php
// htmlspecialchars convierte caracteres especiales en texto para que no se ejecute como código ejecutable
function especial($text) {
    // Si $text es null, usa una cadena vacía ('') para evitar errores.
    return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8'); // ENT_QUOTES se asegura de convertir comillas simples y dobles se conviertan correctamente
}

// old es una utilidad para mantener los datos del formulario si la validación contra el servidor ha sido incorrecta
function old($field, $default = '') {
    static $old = null; // estática para que no se ejecute todo el tiempo, caduca junto al ciclo de vida de la página
    if ($old === null) {
        $old = $_SESSION['old'] ?? []; // Carga los datos del formulario antes de que fallara
        unset($_SESSION['old']); // Borra datos del formulario para que no se vuelvan a repetir 
    }
    return e($old[$field] ?? $default); // Devuelve el valor field del array old, si esta vacío devuelve default que al no estar definida devuelve una cadena vacía
}


function show_success() {
    if (isset($_SESSION['success'])) { // Si la success en el array de session no es null
        $message = $_SESSION['success'];
        unset($_SESSION['success']); // Borramos success
        // El mensaje se escapa (e()) por seguridad antes de insertarlo en el HTML.
        return '<div class="alert alert-success">' . e($message) . '</div>'; // Y mostramos mensaje
    }
    return ''; // Si no nada
}

// Lo mismo que arriba pero con error
function show_error() {
    if (isset($_SESSION['error'])) {
        $message = $_SESSION['error'];
        unset($_SESSION['error']);
        return '<div class="alert alert-danger">' . e($message) . '</div>';
    }
    return '';
}


function sanitize_text($text) {
    return trim(strip_tags($text)); // strip_tags remueve etiquetas html y php, trim elimina espacios en blanco
}


function redirect_with_message($url, $message, $is_error = false) {
    // 1. Almacena el mensaje en la sesión (como flash message)
    if ($is_error) {
        $_SESSION['error'] = $message;
    } else {
        $_SESSION['success'] = $message;
    }
    // Forzamos redirección por si presione f5 no vuelva a reenviar el formulario, esto pasa porque el navegador intenta hacer la última petición http y si la última es el envío del formulario lo repetiría
    header("Location: $url");
    // Detiene la ejecución del script inmediatamente
    exit;
}


function get_preferences_styles() {
    // Obtiene el valor de la cookie 'tema' o 'claro' por defecto.
    $tema = $_COOKIE['tema'] ?? 'claro';
    // Obtiene el valor de la cookie 'tamaño_letra' o 'normal' por defecto.
    $tamaño = $_COOKIE['tamaño_letra'] ?? 'normal';
    
    // Devuelve los atributos data-*, listos para ser insertados en el tag <body>.
    return "data-tema=\"$tema\" data-tamaño=\"$tamaño\"";
}


function has_preferences() {
    return isset($_COOKIE['tema']) || isset($_COOKIE['tamaño_letra']);
}


function clear_preferences() {
    // setcookie() con una marca de tiempo negativa elimina la cookie.
    setcookie('tema', '', time() - 3600, '/');
    setcookie('tamaño_letra', '', time() - 3600, '/');
}
?>