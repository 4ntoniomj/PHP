<?php
// INICIA SESIÓN PRIMERO
session_start();

require_once '../app/auth.php';
require_once '../app/utils.php';

require_login(); // Protege la página
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Dashboard</h1>
            <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Cerrar Sesión
            </a>
        </div>
        
        <p class="text-lg">Bienvenido, <strong><?= especial(get_current_username()) ?></strong>!</p>
        <p class="text-gray-600 mt-2">Has iniciado sesión correctamente.</p>
    </div>
</body>
</html>