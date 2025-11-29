<?php
session_start();
require_once '../app/pdo.php';
require_once '../app/auth.php';
require_once '../app/csrf.php';
require_once '../app/utils.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) die('❌ TOKEN CSRF INVÁLIDO');
    
    $pdo = getPDO();
    $username = sanitize_text($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || strlen($username) < 3) {
        $_SESSION['old'] = ['username' => $username];
        redirect_with_message('register.php', 'Usuario mínimo 3 caracteres', true);
    }
    
    if (strlen($password) < 6) {
        $_SESSION['old'] = ['username' => $username];
        redirect_with_message('register.php', 'Contraseña mínimo 6 caracteres', true);
    }
    
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $pdo->prepare("INSERT INTO usuarios (username, password_hash) VALUES (?, ?)")
             ->execute([$username, $hash]);
        redirect_with_message('login.php', '✅ Usuario creado correctamente');
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['old'] = ['username' => $username];
            redirect_with_message('register.php', '⚠️ El usuario ya existe', true);
        }
        error_log("Error registro: " . $e->getMessage());
        redirect_with_message('register.php', '❌ Error en el registro', true);
    }
}

$token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <style>
        body {
            margin: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2017/10/31/19/05/web-design-2906159_1280.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .fade-overlay {
            /* 🔽 FONDO LILA CON OPACIDAD 0.88 */
            background-color: rgba(245, 240, 255, 0.88);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .register-box {
            position: relative;
            width: 100%;
            max-width: 28rem;
            background-color: #ffffff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            z-index: 1;
        }

        .header { text-align: center; margin-bottom: 2rem; }
        .title {
            font-size: 1.875rem;
            font-weight: 800;
            color: #1f2937;
            margin-top: 1rem;
        }
        .subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }
        .alert-danger {
            background-color: #fee2e2;
            color: #b91c1c;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        .form-group { margin-bottom: 1.5rem; }
        label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 1rem;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        .btn-primary {
            width: 100%;
            padding: 0.75rem;
            background-color: #7c3aed;
            color: white;
            font-weight: 500;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-primary:hover {
            background-color: #6d28d9;
        }
        .link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .link a {
            color: #7c3aed;
            font-weight: 500;
            text-decoration: none;
        }
        .link a:hover {
            color: #6d28d9;
        }
    </style>
</head>
<body>
    <!-- 🔽 FADE LILA -->
    <div class="fade-overlay"></div>

    <div class="register-box">
        <!-- Mensajes -->
        <?= show_error() ?>
        <?= show_success() ?>

        <!-- Header -->
        <div class="header">
            <h1 class="title">Crear Cuenta</h1>
            <p class="subtitle">Regístrate para acceder a la aplicación</p>
        </div>

        <!-- Formulario -->
        <form method="POST" action="register.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>">
            
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" id="username" name="username" value="<?= old('username') ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
                <small style="color: #6b7280;">Mínimo 6 caracteres</small>
            </div>

            <button type="submit" class="btn-primary">Registrarse</button>
        </form>

        <div class="link">
            <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
        </div>
    </div>
</body>
</html>