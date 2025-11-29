<?php
session_start();
require_once '../app/auth.php';
require_once '../app/utils.php';
require_once '../app/pdo.php';
require_once '../app/csrf.php';

require_login();

// Obtener preferencias del usuario
$tamaño_actual = $_COOKIE['tamaño_letra'] ?? 'normal';
$color_fade_actual = $_COOKIE['color_fade'] ?? '#f5f0ff';
$fade_color = $color_fade_actual . 'e0'; // Agregar transparencia
$body_class = "body-$tamaño_actual";

$pdo = getPDO();

// Si viene ID es edición, si no es creación
$id = $_GET['id'] ?? null;
$ticket = null;
$errors = [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);

if ($id) {
    // Modo edición - cargar ticket existente
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    $ticket = $stmt->fetch();
    
    if (!$ticket) {
        $_SESSION['error'] = "Ticket no encontrado";
        header("Location: items_list.php");
        exit;
    }
}

// Procesar POST (envío del formulario)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar CSRF
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['error'] = "Token de seguridad inválido";
        header("Location: items_list.php");
        exit;
    }

    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $prioridad = $_POST['prioridad'] ?? 'media';
    $estado = $_POST['estado'] ?? 'abierta';

    // VALIDACIÓN EN SERVIDOR
    if (empty($titulo)) {
        $errors['titulo'] = "El título es obligatorio";
    } elseif (strlen($titulo) < 5) {
        $errors['titulo'] = "El título debe tener al menos 5 caracteres";
    } elseif (strlen($titulo) > 255) {
        $errors['titulo'] = "El título no puede exceder 255 caracteres";
    }
    
    if (empty($descripcion)) {
        $errors['descripcion'] = "La descripción es obligatoria";
    } elseif (strlen($descripcion) < 10) {
        $errors['descripcion'] = "La descripción debe tener al menos 10 caracteres";
    }
    
    if (!in_array($prioridad, ['baja', 'media', 'alta', 'critica'])) {
        $errors['prioridad'] = "Prioridad no válida";
    }
    
    if (!in_array($estado, ['abierta', 'en_progreso', 'resuelta', 'cerrada'])) {
        $errors['estado'] = "Estado no válido";
    }

    // Si no hay errores, guardar
    if (empty($errors)) {
        try {
            if ($id) {
                // ACTUALIZAR
                $stmt = $pdo->prepare("UPDATE tickets SET titulo = ?, descripcion = ?, prioridad = ?, estado = ? WHERE id = ? AND usuario_id = ?");
                $stmt->execute([$titulo, $descripcion, $prioridad, $estado, $id, $_SESSION['user_id']]);
                $_SESSION['success'] = "Ticket actualizado correctamente";
            } else {
                // CREAR NUEVO
                $stmt = $pdo->prepare("INSERT INTO tickets (titulo, descripcion, prioridad, estado, usuario_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$titulo, $descripcion, $prioridad, $estado, $_SESSION['user_id']]);
                $_SESSION['success'] = "Ticket creado correctamente";
            }
            
            // PRG (Post-Redirect-Get)
            header("Location: items_list.php");
            exit;
            
        } catch (PDOException $e) {
            $errors['general'] = "Error al guardar el ticket: " . $e->getMessage();
        }
    }
    
    // Si hay errores, guardar datos para repintar
    if (!empty($errors)) {
        $_SESSION['old'] = [
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'prioridad' => $prioridad,
            'estado' => $estado
        ];
        $_SESSION['errors'] = $errors;
        header("Location: items_form.php" . ($id ? "?id=$id" : ""));
        exit;
    }
}

// Cargar errores de sesión
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

// Si hay datos viejos (de validación fallida), usarlos
if (!empty($old)) {
    $titulo = $old['titulo'];
    $descripcion = $old['descripcion'];
    $prioridad = $old['prioridad'];
    $estado = $old['estado'];
} elseif ($ticket) {
    // Si es edición, cargar datos del ticket
    $titulo = $ticket['titulo'];
    $descripcion = $ticket['descripcion'];
    $prioridad = $ticket['prioridad'];
    $estado = $ticket['estado'];
} else {
    // Valores por defecto para nuevo ticket
    $titulo = '';
    $descripcion = '';
    $prioridad = 'media';
    $estado = 'abierta';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $id ? 'Editar' : 'Nuevo' ?> Ticket - Gestor de Incidencias</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        /* Tamaños de fuente aplicados correctamente */
        body.body-pequeño { font-size: 14px; }
        body.body-normal { font-size: 16px; }
        body.body-grande { font-size: 18px; }
        body.body-muy-grande { font-size: 20px; }

        /* FADE DINÁMICO CON PREFERENCIAS */
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

        /* Enlace preferencias */
        .preferences-link {
            position: absolute;
            top: 1rem;
            right: 2rem;
            background: rgba(255, 255, 255, 0.9);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            color: #7c3aed;
            font-weight: 500;
            z-index: 10;
            transition: background 0.3s;
            font-size: 1em;
        }

        .preferences-link:hover {
            background: white;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 1.5em;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1em;
        }
        
        .nav-links {
            display: flex;
            gap: 1rem;
        }
        
        .nav-links a {
            color: #3498db;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 1em;
        }
        
        .nav-links a:hover {
            background: #ecf0f1;
            transform: translateY(-2px);
        }
        
        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 1em;
        }
        
        .logout-btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
        }
        
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 2rem;
            flex: 1;
        }
        
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #ecf0f1;
        }

        .form-header h2 {
            color: #2c3e50;
            font-size: 1.8em;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #7f8c8d;
            font-size: 1.1em;
        }
        
        .form-group {
            margin-bottom: 1.8rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.7rem;
            color: #2c3e50;
            font-weight: 600;
            font-size: 1em;
        }
        
        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1em;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #3498db;
            background: white;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            transform: translateY(-2px);
        }
        
        textarea {
            min-height: 140px;
            resize: vertical;
            line-height: 1.5;
        }
        
        .error {
            color: #e74c3c;
            font-size: 0.875em;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .error::before {
            content: "⚠";
            font-size: 0.8em;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #ecf0f1;
        }
        
        .btn {
            padding: 0.9rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-weight: 600;
            min-width: 140px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9, #2573a7);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: white;
            box-shadow: 0 4px 15px rgba(149, 165, 166, 0.3);
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #7f8c8d, #6c7b7d);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(149, 165, 166, 0.4);
        }
        
        .alert {
            padding: 1.2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            border-left: 4px solid;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            font-size: 1em;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left-color: #e74c3c;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left-color: #27ae60;
        }

        .priority-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .priority-baja { background-color: #27ae60; }
        .priority-media { background-color: #f39c12; }
        .priority-alta { background-color: #e74c3c; }
        .priority-critica { background-color: #8e44ad; }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 1rem;
                padding: 1rem;
            }
            
            .user-info {
                flex-direction: column;
                gap: 1rem;
            }
            
            .nav-links {
                order: -1;
            }
            
            .container {
                padding: 0 1rem;
                margin: 1rem auto;
            }
            
            .form-container {
                padding: 1.5rem;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            .preferences-link {
                position: relative;
                top: auto;
                right: auto;
                margin: 1rem;
                align-self: flex-end;
            }
        }
    </style>
</head>
<body class="<?= $body_class ?>">
    <!-- FADE DINÁMICO CON PREFERENCIAS -->
    <div class="fade-overlay"></div>

    <div class="main-container">
        <!-- Enlace preferencias -->
        <a href="preferencias.php" class="preferences-link">🎨 Preferencias</a>

        <!-- Header -->
        <div class="header">
            <h1>🎫 <?= $id ? 'Editar' : 'Nuevo' ?> Ticket</h1>
            <div class="user-info">
                <div class="nav-links">
                    <a href="index.php">🏠 Inicio</a>
                    <a href="items_list.php">📋 Lista de Tickets</a>
                </div>
                <span>Hola, <strong><?= especial($_SESSION['username']) ?></strong></span>
                <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="container">
            <?= show_success() ?>
            <?= show_error() ?>

            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger">
                    <?= especial($errors['general']) ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <div class="form-header">
                    <h2><?= $id ? 'Editar Ticket Existente' : 'Crear Nuevo Ticket' ?></h2>
                    <p><?= $id ? 'Modifica la información del ticket según sea necesario' : 'Completa todos los campos para registrar un nuevo ticket' ?></p>
                </div>

                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token() ?>">
                    
                    <div class="form-group">
                        <label for="titulo">Título *</label>
                        <input type="text" 
                               id="titulo" 
                               name="titulo" 
                               value="<?= old('titulo', $titulo) ?>"
                               required
                               maxlength="255"
                               placeholder="Ingresa un título descriptivo para el ticket">
                        <?php if (isset($errors['titulo'])): ?>
                            <div class="error"><?= especial($errors['titulo']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="descripcion">Descripción *</label>
                        <textarea 
                            id="descripcion" 
                            name="descripcion" 
                            required
                            placeholder="Describe detalladamente el problema, solicitud o incidencia..."><?= old('descripcion', $descripcion) ?></textarea>
                        <?php if (isset($errors['descripcion'])): ?>
                            <div class="error"><?= especial($errors['descripcion']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="prioridad">Prioridad</label>
                        <select id="prioridad" name="prioridad">
                            <option value="baja" <?= $prioridad === 'baja' ? 'selected' : '' ?>>Baja</option>
                            <option value="media" <?= $prioridad === 'media' ? 'selected' : '' ?>>Media</option>
                            <option value="alta" <?= $prioridad === 'alta' ? 'selected' : '' ?>>Alta</option>
                            <option value="critica" <?= $prioridad === 'critica' ? 'selected' : '' ?>>Crítica</option>
                        </select>
                        <?php if (isset($errors['prioridad'])): ?>
                            <div class="error"><?= especial($errors['prioridad']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="abierta" <?= $estado === 'abierta' ? 'selected' : '' ?>>Abierta</option>
                            <option value="en_progreso" <?= $estado === 'en_progreso' ? 'selected' : '' ?>>En Progreso</option>
                            <option value="resuelta" <?= $estado === 'resuelta' ? 'selected' : '' ?>>Resuelta</option>
                            <option value="cerrada" <?= $estado === 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
                        </select>
                        <?php if (isset($errors['estado'])): ?>
                            <div class="error"><?= especial($errors['estado']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-actions">
                        <a href="items_list.php" class="btn btn-secondary">↶ Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <?= $id ? '✏️ Actualizar' : '➕ Crear' ?> Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>