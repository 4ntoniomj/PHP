<?php
session_start();
require_once '../app/auth.php';
require_once '../app/utils.php';
require_once '../app/pdo.php';

require_login();

// Obtener preferencias del usuario
$tamaño_actual = $_COOKIE['tamaño_letra'] ?? 'normal';
$color_fade_actual = $_COOKIE['color_fade'] ?? '#f5f0ff';
$fade_color = $color_fade_actual . 'e0'; // Agregar transparencia
$body_class = "body-$tamaño_actual";

$pdo = getPDO();

$id = $_GET['id'] ?? null;

if (!$id) {
    $_SESSION['error'] = "Ticket no especificado";
    header("Location: items_list.php");
    exit;
}

// Obtener ticket
$stmt = $pdo->prepare("
    SELECT t.*, u.username 
    FROM tickets t 
    JOIN usuarios u ON t.usuario_id = u.id 
    WHERE t.id = ? AND t.usuario_id = ?
");
$stmt->execute([$id, $_SESSION['user_id']]);
$ticket = $stmt->fetch();

if (!$ticket) {
    $_SESSION['error'] = "Ticket no encontrado";
    header("Location: items_list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #<?= especial($ticket['id']) ?> - Gestor de Incidencias</title>
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
            backdrop-filter: blur(10px);
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
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 2rem;
            flex: 1;
        }
        
        .ticket-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .ticket-header {
            background: linear-gradient(135deg, #34495e, #2c3e50);
            color: white;
            padding: 2rem;
        }
        
        .ticket-title {
            font-size: 1.8em;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .ticket-meta {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            font-size: 0.9em;
            opacity: 0.9;
        }
        
        .ticket-body {
            padding: 2rem;
        }
        
        .section {
            margin-bottom: 2rem;
        }
        
        .section:last-child {
            margin-bottom: 0;
        }
        
        .section-title {
            color: #2c3e50;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #ecf0f1;
            font-size: 1.3em;
            font-weight: 600;
        }
        
        .description {
            line-height: 1.6;
            white-space: pre-wrap;
            font-size: 1.1em;
            color: #4a5568;
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #3498db;
        }
        
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .estado-abierta { background: #ffebee; color: #e74c3c; }
        .estado-en_progreso { background: #fff3e0; color: #f39c12; }
        .estado-resuelta { background: #e8f5e8; color: #27ae60; }
        .estado-cerrada { background: #f0f0f0; color: #7f8c8d; }
        
        .prioridad-alta { background: #ffebee; color: #e74c3c; }
        .prioridad-media { background: #fff3e0; color: #f39c12; }
        .prioridad-baja { background: #e8f5e8; color: #27ae60; }
        .prioridad-critica { background: #fce4ec; color: #c2185b; }
        
        .actions {
            display: flex;
            gap: 1rem;
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #ecf0f1;
        }
        
        .btn {
            padding: 0.9rem 1.5rem;
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
        
        .btn-danger {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid #3498db;
            transition: transform 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .info-label {
            font-size: 0.8em;
            color: #7f8c8d;
            margin-bottom: 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-weight: 600;
            font-size: 1.1em;
            color: #2c3e50;
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
        }

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
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .container {
                padding: 0 1rem;
                margin: 1rem auto;
            }
            
            .ticket-header {
                padding: 1.5rem;
            }
            
            .ticket-body {
                padding: 1.5rem;
            }
            
            .ticket-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
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
            <h1>🎫 Detalle del Ticket</h1>
            <div class="user-info">
                <div class="nav-links">
                    <a href="index.php">🏠 Inicio</a>
                    <a href="items_list.php">📋 Lista de Tickets</a>
                    <a href="items_form.php">➕ Nuevo Ticket</a>
                    <a href="items_log.php  " style="color: #e74c3c;">🗑️ Log</a>
                </div>
                <span>Hola, <strong><?= especial($_SESSION['username']) ?></strong></span>
                <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>

        <div class="container">
            <?= show_success() ?>
            <?= show_error() ?>

            <div class="ticket-card">
                <div class="ticket-header">
                    <h1 class="ticket-title"><?= especial($ticket['titulo']) ?></h1>
                    <div class="ticket-meta">
                        <span>🎫 Ticket #<?= especial($ticket['id']) ?></span>
                        <span>👤 Creado por: <?= especial($ticket['username']) ?></span>
                        <span>📅 Fecha: <?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?></span>
                    </div>
                </div>

                <div class="ticket-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Estado</div>
                            <div class="info-value">
                                <span class="badge estado-<?= especial($ticket['estado']) ?>">
                                    📊 <?= ucfirst(str_replace('_', ' ', especial($ticket['estado']))) ?>
                                </span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Prioridad</div>
                            <div class="info-value">
                                <span class="badge prioridad-<?= especial($ticket['prioridad']) ?>">
                                    <span class="priority-indicator prioridad-<?= especial($ticket['prioridad']) ?>"></span>
                                    ⚡ <?= ucfirst(especial($ticket['prioridad'])) ?>
                                </span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Última actualización</div>
                            <div class="info-value">
                                🔄 <?= date('d/m/Y H:i', strtotime($ticket['updated_at'] ?? $ticket['created_at'])) ?>
                            </div>
                        </div>
                    </div>

                    <div class="section">
                        <h3 class="section-title">📋 Descripción</h3>
                        <div class="description"><?= nl2br(especial($ticket['descripcion'])) ?></div>
                    </div>

                    <div class="actions">
                        <a href="items_list.php" class="btn btn-secondary">← Volver al Listado</a>
                        <a href="items_form.php?id=<?= especial($ticket['id']) ?>" class="btn btn-primary">✏️ Editar Ticket</a>
                        <a href="items_delete.php?id=<?= especial($ticket['id']) ?>" 
                           class="btn btn-danger"
                           onclick="return confirm('¿Estás seguro de que quieres eliminar este ticket?')">🗑️ Eliminar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>