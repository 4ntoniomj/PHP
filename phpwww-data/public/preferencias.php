<?php
session_start();
require_once '../app/auth.php';
require_once '../app/utils.php';
require_once '../app/csrf.php';

require_login();

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        die('Token CSRF inválido.');
    }

    $tema = sanitize_text($_POST['tema'] ?? 'claro');
    $tamaño_letra = sanitize_text($_POST['tamaño_letra'] ?? 'normal');

    setcookie('tema', $tema, time() + (30 * 24 * 60 * 60), '/');
    setcookie('tamaño_letra', $tamaño_letra, time() + (30 * 24 * 60 * 60), '/');

    $_SESSION['success'] = "Preferencias guardadas.";
    header("Location: preferencias.php");
    exit;
}

$tema_actual = $_COOKIE['tema'] ?? 'claro';
$tamaño_actual = $_COOKIE['tamaño_letra'] ?? 'normal';

// Colores de fade según tema
$fades = [
    'claro' => 'rgba(245, 240, 255, 0.88)',
    'oscuro' => 'rgba(30, 30, 40, 0.85)',
    'azul' => 'rgba(200, 220, 255, 0.85)',
];
$fade_color = $fades[$tema_actual] ?? $fades['claro'];
?>
<!DOCTYPE html>
<html lang="es" <?= get_preferences_styles() ?>>
<head>
    <meta charset="UTF-8">
    <title>Preferencias</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2017/10/31/19/05/web-design-2906159_1280.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            min-height: 100vh;
            position: relative;
        }

        .fade-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-color: <?= $fade_color ?>;
        }

        body[data-tema="claro"] { color: #333; background-color: #f5f6fa; }
        body[data-tema="oscuro"] { color: #fff; background-color: #1a1a1a; }
        body[data-tema="azul"] { color: #003366; background-color: #e3f2fd; }

        body[data-tamaño="pequeño"] { font-size: 14px; }
        body[data-tamaño="normal"] { font-size: 16px; }
        body[data-tamaño="grande"] { font-size: 18px; }
        body[data-tamaño="muy-grande"] { font-size: 20px; }

        h1, label, button {
            font-size: inherit;
        }

        .container {
            max-width: 600px;
            margin: 3rem auto;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }

        .form-group input[type="radio"] {
            margin-right: 0.5rem;
        }

        button {
            padding: 0.6rem 1.2rem;
            background-color: #7c3aed;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #6d28d9;
        }

        a {
            color: #7c3aed;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="fade-overlay"></div>

    <div class="container">
        <h1>Preferencias de usuario</h1>
        <?= show_success() ?>
        <?= show_error() ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= especial(generate_csrf_token()) ?>">

            <div class="form-group">
                <label>Tema:</label>
                <label><input type="radio" name="tema" value="claro" <?= $tema_actual === 'claro' ? 'checked' : '' ?>> Claro</label><br>
                <label><input type="radio" name="tema" value="oscuro" <?= $tema_actual === 'oscuro' ? 'checked' : '' ?>> Oscuro</label><br>
                <label><input type="radio" name="tema" value="azul" <?= $tema_actual === 'azul' ? 'checked' : '' ?>> Azul</label><br>
            </div>

            <div class="form-group">
                <label>Tamaño de letra:</label>
                <label><input type="radio" name="tamaño_letra" value="pequeño" <?= $tamaño_actual === 'pequeño' ? 'checked' : '' ?>> Pequeño</label><br>
                <label><input type="radio" name="tamaño_letra" value="normal" <?= $tamaño_actual === 'normal' ? 'checked' : '' ?>> Normal</label><br>
                <label><input type="radio" name="tamaño_letra" value="grande" <?= $tamaño_actual === 'grande' ? 'checked' : '' ?>> Grande</label><br>
                <label><input type="radio" name="tamaño_letra" value="muy-grande" <?= $tamaño_actual === 'muy-grande' ? 'checked' : '' ?>> Muy grande</label><br>
            </div>

            <button type="submit">Guardar</button>
        </form>

        <p><a href="index.php">Volver al inicio</a></p>
    </div>
</body>
</html>