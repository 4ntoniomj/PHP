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

    $tamaño_letra = sanitize_text($_POST['tamaño_letra'] ?? 'normal');
    $color_fade = sanitize_text($_POST['color_fade'] ?? '#f5f0ff');

    // Validar que el color sea hexadecimal
    if (!preg_match('/^#[a-f0-9]{6}$/i', $color_fade)) {
        $color_fade = '#f5f0ff'; // Color por defecto si no es válido
    }

    setcookie('tamaño_letra', $tamaño_letra, time() + (30 * 24 * 60 * 60), '/');
    setcookie('color_fade', $color_fade, time() + (30 * 24 * 60 * 60), '/');

    $_SESSION['success'] = "Preferencias guardadas.";
    header("Location: preferencias.php");
    exit;
}

$tamaño_actual = $_COOKIE['tamaño_letra'] ?? 'normal';
$color_fade_actual = $_COOKIE['color_fade'] ?? '#f5f0ff';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preferencias</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
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
            background-color: <?= $color_fade_actual ?>e0;
        }

        /* Tamaños de fuente aplicados correctamente */
        body.body-pequeño { font-size: 14px; }
        body.body-normal { font-size: 16px; }
        body.body-grande { font-size: 18px; }
        body.body-muy-grande { font-size: 20px; }

        .container {
            max-width: 700px;
            margin: 3rem auto;
            padding: 2.5rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e1e5e9;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }

        .form-group label {
            display: block;
            margin-bottom: 1rem;
            font-weight: 600;
            color: #2c3e50;
            font-size: 1.1rem;
        }

        .radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .radio-option {
            display: flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 6px;
            border: 2px solid #e1e5e9;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .radio-option:hover {
            border-color: #3498db;
            transform: translateY(-2px);
        }

        .radio-option input[type="radio"] {
            margin-right: 0.5rem;
        }

        .color-picker-group {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .color-preview {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            border: 2px solid #ddd;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .color-preview:hover {
            transform: scale(1.1);
        }

        input[type="color"] {
            width: 60px;
            height: 40px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        input[type="text"] {
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 6px;
            font-size: 1rem;
            width: 120px;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e1e5e9;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9, #2573a7);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
        }

        .predefined-colors {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
            flex-wrap: wrap;
        }

        .predefined-color {
            width: 30px;
            height: 30px;
            border-radius: 4px;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .predefined-color:hover {
            transform: scale(1.2);
            border-color: #333;
        }

        .current-color {
            font-weight: normal;
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-left: 1rem;
        }

        @media (max-width: 768px) {
            .container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .form-actions {
                flex-direction: column;
                gap: 1rem;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body class="body-<?= $tamaño_actual ?>">
    <div class="fade-overlay"></div>

    <div class="container">
        <div class="header">
            <h1>🎨 Preferencias de Usuario</h1>
            <a href="index.php" class="btn btn-secondary">🏠 Inicio</a>
        </div>

        <?= show_success() ?>
        <?= show_error() ?>

        <form method="POST" id="preferences-form">
            <input type="hidden" name="csrf_token" value="<?= especial(generate_csrf_token()) ?>">

            <div class="form-group">
                <label>Tamaño de Letra:</label>
                <div class="radio-group">
                    <label class="radio-option">
                        <input type="radio" name="tamaño_letra" value="pequeño" <?= $tamaño_actual === 'pequeño' ? 'checked' : '' ?>> 
                        Pequeño
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="tamaño_letra" value="normal" <?= $tamaño_actual === 'normal' ? 'checked' : '' ?>> 
                        Normal
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="tamaño_letra" value="grande" <?= $tamaño_actual === 'grande' ? 'checked' : '' ?>> 
                        Grande
                    </label>
                    <label class="radio-option">
                        <input type="radio" name="tamaño_letra" value="muy-grande" <?= $tamaño_actual === 'muy-grande' ? 'checked' : '' ?>> 
                        Muy Grande
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>
                    Color de Fondo (Fade): 
                    <span class="current-color">Color actual: <?= $color_fade_actual ?></span>
                </label>
                <div class="color-picker-group">
                    <input type="color" id="color_picker" name="color_fade" value="<?= $color_fade_actual ?>" 
                           onchange="updateColorPreview(this.value)">
                    <div class="color-preview" id="color_preview" style="background-color: <?= $color_fade_actual ?>"></div>
                    <input type="text" id="color_text" value="<?= $color_fade_actual ?>" 
                           placeholder="#FFFFFF" maxlength="7"
                           onchange="updateColorFromText(this.value)">
                    
                    <div class="predefined-colors">
                        <div class="predefined-color" style="background-color: #f5f0ff" onclick="setColor('#f5f0ff')" title="Lila Claro"></div>
                        <div class="predefined-color" style="background-color: #e3f2fd" onclick="setColor('#e3f2fd')" title="Azul Claro"></div>
                        <div class="predefined-color" style="background-color: #f3e5f5" onclick="setColor('#f3e5f5')" title="Lila Pastel"></div>
                        <div class="predefined-color" style="background-color: #e8f5e8" onclick="setColor('#e8f5e8')" title="Verde Claro"></div>
                        <div class="predefined-color" style="background-color: #fff3e0" onclick="setColor('#fff3e0')" title="Naranja Claro"></div>
                        <div class="predefined-color" style="background-color: #fce4ec" onclick="setColor('#fce4ec')" title="Rosa Pastel"></div>
                        <div class="predefined-color" style="background-color: #1e1e28" onclick="setColor('#1e1e28')" title="Oscuro"></div>
                        <div class="predefined-color" style="background-color: #2c3e50" onclick="setColor('#2c3e50')" title="Azul Oscuro"></div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="logout.php" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres cerrar sesión?')">
                    🚪 Cerrar Sesión
                </a>
                <button type="submit" class="btn btn-primary">💾 Guardar Preferencias</button>
            </div>
        </form>
    </div>

    <script>
        function updateColorPreview(color) {
            document.getElementById('color_preview').style.backgroundColor = color;
            document.getElementById('color_text').value = color;
            updateCurrentColorText(color);
        }

        function updateColorFromText(color) {
            if (/^#[0-9A-F]{6}$/i.test(color)) {
                document.getElementById('color_picker').value = color;
                document.getElementById('color_preview').style.backgroundColor = color;
                updateCurrentColorText(color);
            }
        }

        function setColor(color) {
            document.getElementById('color_picker').value = color;
            document.getElementById('color_text').value = color;
            document.getElementById('color_preview').style.backgroundColor = color;
            updateCurrentColorText(color);
        }

        function updateCurrentColorText(color) {
            const currentColorElement = document.querySelector('.current-color');
            if (currentColorElement) {
                currentColorElement.textContent = 'Color actual: ' + color;
            }
        }

        // Actualizar el texto del color actual al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            updateCurrentColorText('<?= $color_fade_actual ?>');
        });
    </script>
</body>
</html>