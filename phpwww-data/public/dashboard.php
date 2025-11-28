<?php
session_start();
require_once __DIR__ . '/../app/auth.php';
require_once __DIR__ . '/../app/utils.php';

require_login(); // Protege la página
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f3f4f6;
            padding: 2rem;
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .title {
            font-size: 2rem;
            font-weight: bold;
            color: #1f2937;
        }
        
        .logout-btn {
            background-color: #dc2626;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.2s;
        }
        
        .logout-btn:hover {
            background-color: #b91c1c;
        }
        
        .welcome {
            font-size: 1.25rem;
            color: #374151;
        }
        
        .welcome strong {
            color: #1f2937;
        }
        
        .info {
            color: #6b7280;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Panel de Control</h1>
            <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
        </div>
        
        <p class="welcome">
            Bienvenido, <strong><?= especial(get_current_username()) ?></strong>!
        </p>
        <p class="info">
            Has iniciado sesión correctamente.
        </p>
    </div>
</body>
</html>