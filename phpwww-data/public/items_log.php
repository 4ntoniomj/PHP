<?php
// ==================== INICIO Y ARCHIVOS NECESARIOS ====================
session_start();                     
require_once '../app/auth.php';      
require_once '../app/utils.php';     
require_once '../app/csrf.php';      
require_login();  // Obliga a que solo usuarios logueados puedan acceder

// ==================== PROCESAR FORMULARIO (POST) ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    //Verificación de Token CSRF 
    $token = $_POST['csrf_token'] ?? '';     
    if (!verify_csrf_token($token)) {        
        die('Token CSRF inválido.');         
    }

    // Obtener y limpiar datos enviados 
    $tamaño_letra = sanitize_text($_POST['tamaño_letra'] ?? 'normal');  
    $color_fade = sanitize_text($_POST['color_fade'] ?? '#f5f0ff');     

    // Validar formato del color (solo HEX) 
    if (!preg_match('/^#[a-f0-9]{6}$/i', $color_fade)) { 
        $color_fade = '#f5f0ff'; 
    }

    //  Guardar preferencias en cookies (duración: 30 días)
    setcookie('tamaño_letra', $tamaño_letra, time() + (30 * 24 * 60 * 60), '/');
    setcookie('color_fade', $color_fade, time() + (30 * 24 * 60 * 60), '/');

    // Mensaje de éxito en la sesión
    $_SESSION['success'] = "Preferencias guardadas.";

    // Redirigir para evitar reenvío de formulario
    header("Location: preferencias.php");
    exit;
}

// ==================== LEER PREFERENCIAS ACTUALES (DESDE COOKIES) ====================
$tamaño_actual = $_COOKIE['tamaño_letra'] ?? 'normal';   
$color_fade_actual = $_COOKIE['color_fade'] ?? '#f5f0ff'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Papelera de Reciclaje (item_log)</title>
    <style>
        /* Estilos base copiados de items_list.php */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2017/10/31/19/05/web-design-2906159_1280.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            min-height: 100vh;
            position: relative;
            padding: 0;
            font-size: 16px;
        }

        /* Tamaños de fuente dinámicos */
        body.body-pequeño { font-size: 14px; }
        body.body-normal { font-size: 16px; }
        body.body-grande { font-size: 18px; }
        body.body-muy-grande { font-size: 20px; }

        .fade-overlay {
            background-color: <?= $fade_color ?>;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .main-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 { color: #2c3e50; font-size: 1.5em; }

        /* Estilos específicos de la Papelera */
        .container {
            max-width: 1200px; margin: 2rem auto; padding: 0 2rem; flex: 1; width: 100%;
        }

        .header-content {
            background: rgba(255, 255, 255, 0.95);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .btn-back { text-decoration: none; color: #3498db; font-weight: bold; padding: 0.5rem 1rem; border-radius: 5px; }
        .btn-back:hover { background: #ecf0f1; }

        .trash-grid { 
            display: grid; 
            gap: 1.5rem; 
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); 
        }

        .trash-card { 
            background: rgba(255, 255, 255, 0.95); 
            padding: 1.5rem; 
            border-radius: 8px; 
            border-left: 5px solid #7f8c8d; /* Gris para tickets eliminados */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); 
            display: flex; 
            flex-direction: column; 
            gap: 0.5rem; 
        }

        .trash-title { 
            font-size: 1.2em; 
            font-weight: bold; 
            color: #c0392b; /* Rojo para indicar borrado */
        }
        
        .trash-description {
            font-size: 0.9em;
            color: #555;
            max-height: 4.5em; /* 3 líneas */
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 0.5rem;
        }

        .trash-meta { 
            font-size: 0.85em; 
            color: #7f8c8d; 
            padding-top: 0.5rem;
            border-top: 1px dashed #eee;
        }
        
        .actions { 
            display: flex; 
            gap: 0.5rem; 
            margin-top: auto; 
            padding-top: 1rem; 
        }
        
        .actions form {
            flex: 1;
        }
        
        .btn { 
            padding: 0.75rem 1.5rem; 
            border: none; 
            border-radius: 5px;
            cursor: pointer; 
            text-decoration: none; 
            display: block;
            width: 100%;
            text-align: center; 
            transition: background 0.3s, transform 0.2s; 
            color: white; 
            font-size: 0.9em;
        }
        .btn:hover { transform: translateY(-2px); }

        .btn-restore { background: #27ae60; } /* Verde para Restaurar */
        .btn-restore:hover { background: #219a52; }

        .btn-kill { background: #e74c3c; } /* Rojo para Borrar Definitivo */
        .btn-kill:hover { background: #c0392b; }

        .empty-state { 
            grid-column: 1 / -1; 
            text-align: center; 
            padding: 3rem; 
            background: rgba(255,255,255,0.8); 
            border-radius: 10px; 
            color: #7f8c8d; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

    </style>
</head>
<body class="<?= $body_class ?>">
    <div class="fade-overlay"></div>

    <div class="main-container">
        <div class="header">
            <h1>📝 Lista de Log</h1>
            <a href="items_list.php" class="btn-back">↶ Volver a la Lista</a>
        </div>

        <div class="container">
            <?= show_success() ?>
            <?= show_error() ?>

            <div class="trash-grid">
                <?php if (empty($deleted_tickets)): ?>
                    <div class="empty-state">
                        <h3>La lista de log esta vacía</h3>
                        <p>No hay tickets eliminados recientemente.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($deleted_tickets as $ticket): ?>
                        <div class="trash-card">
                            <div class="trash-title"><?= especial($ticket['titulo']) ?></div>
                            <div class="trash-description">
                                <?= especial(substr($ticket['descripcion'], 0, 150)) ?>...
                            </div>
                            <div class="trash-meta">
                                **ID Original:** <?= especial($ticket['ticket_original_id']) ?><br>
                                **Borrado el:** <?= date('d/m/Y H:i', strtotime($ticket['deleted_at'])) ?>
                            </div>
                            <div class="actions">
                                <form action="items_log.php" method="POST">
                                    <input type="hidden" name="id" value="<?= especial($ticket['id']) ?>">
                                    <input type="hidden" name="action" value="restore">
                                    <button type="submit" class="btn btn-restore">♻️ Restaurar</button>
                                </form>

                                <form action="items_log.php" method="POST" onsubmit="return confirm('¿ESTÁS SEGURO? Esto eliminará el ticket de forma permanente (COMMIT).');">
                                    <input type="hidden" name="id" value="<?= especial($ticket['id']) ?>">
                                    <input type="hidden" name="action" value="kill">
                                    <button type="submit" class="btn btn-kill">❌ Borrar Definitivo</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
