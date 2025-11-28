<?php
require_once '../app/pdo.php';

// Es la función que recauda, guarda y accede a los valores de la sesión
session_start();

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /GDI/public/login.php'); // Si user_id está vacío, osea no tiene sesión iniciada, lo redirige al login
        exit;
    }
}

// Estas tres funciones mantienen logueado al usuario en cualquier página de la aplicación
function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function get_current_username() {
    return $_SESSION['username'] ?? null;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function logout() {
    $_SESSION = [];
    session_destroy();
}


// Asegúrate de que este archivo ha incluido previamente 'require_once '../app/pdo.php';'

/**
 * Busca un usuario por nombre y verifica la contraseña contra su hash en la base de datos.
 *
 * @param string $username El nombre de usuario introducido.
 * @param string $password La contraseña en texto claro introducida.
 * @return array|false Devuelve el array de usuario (id, username) si es válido, o false si no lo es.
 */
function verify_credentials_db(string $username, string $password): array|false
{
    try {
        $pdo = getPDO(); // En una variable el acceso a la base de datos
        
        // Variable que contiene select
        $sql = "SELECT id, username, password_hash FROM usuarios WHERE username = ?";
        
        // Prepara el select
        $stmt = $pdo->prepare($sql);
        // Filtrandolo por el username del formulario
        $stmt->execute([$username]);
        // Si es verdadero obitiene el output del select, si no falso
        $user = $stmt->fetch();
        
        // Si está vacio sale
        if (!$user) {
            return false; // Usuario no encontrado
        }
        
        // Comprueba el hash del formulario con la de la base de datos
        if (password_verify($password, $user['password_hash'])) {
            
            // Si es correcto devuelve los valores
            return [
                'id' => $user['id'],
                'username' => $user['username']
            ];
        }
        
        return false;
        
    } catch (PDOException $e) {
        error_log("Error de BD en login: " . $e->getMessage());
        return false;
    }
}

// Guarda la sesión
function login_user($user_id, $username) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['login_time'] = time();
}
?>