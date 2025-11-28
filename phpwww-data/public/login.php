<?php
// INICIA SESIÓN PRIMERO
session_start();

require_once '../app/utils.php';
require_once '../app/csrf.php';
require_once '../app/auth.php';

// Si ya está logueado, redirigir
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-login-gradient { background: linear-gradient(135deg, #4c51bf 0%, #7c3aed 100%); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-login-gradient p-4">

    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-2xl">
        <!-- MENSAJES -->
        <?= show_error() ?>
        <?= show_success() ?>

        <div class="text-center mb-8">
            <h1 class="mt-4 text-3xl font-extrabold text-gray-900">Bienvenido</h1>
            <p class="mt-2 text-sm text-gray-600">Inicia sesión para acceder a tu cuenta.</p>
        </div>

        <!-- FORMULARIO CORREGIDO -->
        <form method="POST" action="authenticate.php" class="space-y-6">
            <input type="hidden" name="csrf_token" value="<?= old('csrf_token', $token) ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                <input type="text" name="username" value="<?= old('username') ?>" required 
                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <button type="submit" 
                    class="w-full py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Iniciar Sesión
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="mt-4 text-sm text-gray-600">
                ¿No tienes cuenta?
                <a href="register.php" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Regístrate aquí
                </a>
            </p>
        </div>
    </div>

</body>
</html>