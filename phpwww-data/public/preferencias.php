<?php
require_once '../app/auth.php';
require_once '../app/utils.php';
require_once '../app/pdo.php';
?>
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
?>
<!DOCTYPE html>
<html lang="es" <?= get_preferences_styles() ?>>
<head>
    <meta charset="UTF-8">
    <title>Preferencias</title>
</head>
<body>
    <h1>Preferencias de usuario</h1>

    <?= show_success() ?>
    <?= show_error() ?>

    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= especial(generate_csrf_token()) ?>">

        <label>Tema:</label><br>
        <label><input type="radio" name="tema" value="claro" <?= $tema_actual === 'claro' ? 'checked' : '' ?>> Claro</label><br>
        <label><input type="radio" name="tema" value="oscuro" <?= $tema_actual === 'oscuro' ? 'checked' : '' ?>> Oscuro</label><br>
        <label><input type="radio" name="tema" value="azul" <?= $tema_actual === 'azul' ? 'checked' : '' ?>> Azul</label><br><br>

        <label>Tamaño de letra:</label><br>
        <label><input type="radio" name="tamaño_letra" value="pequeño" <?= $tamaño_actual === 'pequeño' ? 'checked' : '' ?>> Pequeño</label><br>
        <label><input type="radio" name="tamaño_letra" value="normal" <?= $tamaño_actual === 'normal' ? 'checked' : '' ?>> Normal</label><br>
        <label><input type="radio" name="tamaño_letra" value="grande" <?= $tamaño_actual === 'grande' ? 'checked' : '' ?>> Grande</label><br>
        <label><input type="radio" name="tamaño_letra" value="muy-grande" <?= $tamaño_actual === 'muy-grande' ? 'checked' : '' ?>> Muy grande</label><br><br>

        <button type="submit">Guardar</button>
    </form>

    <p><a href="index.php">Volver al inicio</a></p>
</body>
</html>