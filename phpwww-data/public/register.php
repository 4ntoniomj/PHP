<?php
// ¡IMPORTANTE! Esto debe estar ABSOLUTAMENTE AL INICIO, sin espacios antes
session_start();

// DEBUG: Si hay error, mostrarlo
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Requiere los archivos con rutas ABSOLUTAS
require_once __DIR__ . '/../app/pdo.php';
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/csrf.php';
require_once __DIR__ . '/../app/utils.php';

// Si ya está logueado, redirigir
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die('❌ TOKEN CSRF INVÁLIDO - POSIBLE ATAQUE');
    }
    
    $pdo = getPDO();
    $username = sanitize_text($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validaciones
    if (empty($username) || strlen($username) < 3) {
        $_SESSION['old'] = ['username' => $username];
        redirect_with_message('register.php', 'Usuario mínimo 3 caracteres', true);
    }
    
    if (strlen($password) < 6) {
        $_SESSION['old'] = ['username' => $username];
        redirect_with_message('register.php', 'Contraseña mínimo 6 caracteres', true);
    }
    
    // Hash seguro
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Guardar en BD
    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
        redirect_with_message('login.php', '✅ Usuario creado correctamente');
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Usuario duplicado
            $_SESSION['old'] = ['username' => $username];
            redirect_with_message('register.php', '⚠️ El usuario ya existe', true);
        }
        error_log("Error registro: " . $e->getMessage());
        redirect_with_message('register.php', '❌ Error en el registro', true);
    }
}

// Generar token CSRF
$token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-600 to-blue-600 p-4">

    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl">
        <h1 class="text-3xl font-extrabold text-center mb-8 text-gray-800">Crear Cuenta</h1>
        
        <!-- MENSAJES -->
        <?= show_error() ?>
        <?= show_success() ?>

        <!-- FORMULARIO SIMPLIFICADO -->
        <form method="POST" action="register.php" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Usuario</label>
                <input type="text" name="username" 
                       value="<?= htmlspecialchars($_SESSION['old']['username'] ?? '') ?>" 
                       required 
                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-500 mt-1">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Contraseña</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-green-500 mt-1">
                <p class="text-xs text-gray-500 mt-1">Mínimo 6 caracteres</p>
            </div>

            <!-- BOTÓN ROBUSTO -->
            <button type="submit" 
                    class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 
                           transition duration-200 font-medium">
                Registrarse
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="login.php" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                ← Volver al Login
            </a>
        </div>
    </div>

</body>
</html>