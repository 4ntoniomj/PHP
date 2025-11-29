<?php
require_once '../app/pdo.php';

// Es la función que recauda, guarda y accede a los valores de la sesión
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header('Location: /public/login.php');
        exit;
    }
}

function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function get_current_username() {
    return $_SESSION['username'] ?? null;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function logout(): void {
    // Solo limpiar si hay sesión activa
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
        
        // Borrar cookie de sesión si existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
}

/**
 * Busca un usuario por nombre y verifica la contraseña contra su hash en la base de datos.
 * Ahora usa USERNAME en lugar de email
 */
function verify_credentials_db(string $username, string $password): array|false {
    try {
        $pdo = getPDO();
        
        // Busca por USERNAME
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM usuarios WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return false;
        }
        
        if (password_verify($password, $user['password_hash'])) {
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

function login_user(int $user_id, string $username) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['login_time'] = time();
}
?>