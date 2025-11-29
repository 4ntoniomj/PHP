<?php
session_start();
require_once '../app/utils.php';
require_once '../app/csrf.php';
require_once '../app/auth.php';

require_login();

$pdo = getPDO();

// Obtener estadísticas
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(estado = 'abierta') as abiertas,
        SUM(estado = 'en_progreso') as en_progreso,
        SUM(estado = 'resuelta') as resueltas,
        SUM(estado = 'cerrada') as cerradas
    FROM tickets 
    WHERE usuario_id = ?
");
$stmt->execute([get_current_user_id()]);
$stats = $stmt->fetch();

// Últimos tickets
$stmt = $pdo->prepare("
    SELECT * FROM tickets 
    WHERE usuario_id = ? 
    ORDER BY created_at DESC 
    LIMIT 5
");
$stmt->execute([get_current_user_id()]);
$latest_tickets = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Gestor de Incidencias</title>
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
        }

        /* 🔽 FADE LILA 0.88 */
        .fade-overlay {
            background-color: rgba(245, 240, 255, 0.88);
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
        }

        .preferences-link:hover {
            background: white;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between; /* CORREGIDO */
            align-items: center;
        }
        
        .header h1 {
            color: #2c3e50;
            font-size: 1.5rem;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logout-btn {
            background: #7c3aed;
            color: white;
            padding: 0.5rem 1rem;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background 0.3s;
        }
        
        .logout-btn:hover {
            background: #6d28d9;
        }
        
        .content {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
            flex: 1;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stat-total { color: #6366f1; }
        .stat-abiertas { color: #ef4444; }
        .stat-progreso { color: #f59e0b; }
        .stat-resueltas { color: #10b981; }
        .stat-cerradas { color: #6b7280; }
        
        .actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            background: #6366f1;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #4f46e5;
        }
        
        .btn-success {
            background: #10b981;
        }
        
        .btn-success:hover {
            background: #059669;
        }
        
        .latest-tickets {
            background: rgba(255, 255, 255, 0.95);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .latest-tickets h2 {
            margin-bottom: 1rem;
            color: #2c3e50;
        }
        
        .ticket-list {
            list-style: none;
        }
        
        .ticket-item {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between; /* CORREGIDO */
            align-items: center;
        }
        
        .ticket-item:last-child {
            border-bottom: none;
        }
        
        .ticket-title {
            font-weight: 500;
            flex-grow: 1;
        }
        
        .ticket-title a {
            color: #4f46e5;
            text-decoration: none;
        }
        
        .ticket-title a:hover {
            text-decoration: underline;
        }
        
        .ticket-meta {
            display: flex;
            gap: 1rem;
            align-items: center;
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .priority-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .priority-alta { background: #fee2e2; color: #dc2626; }
        .priority-media { background: #fef3c7; color: #d97706; }
        .priority-baja { background: #d1fae5; color: #059669; }
        .priority-critica { background: #fce7f3; color: #be185d; }
        
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-danger {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }
    </style>
</head>
<body>
    <!-- 🔽 FADE LILA -->
    <div class="fade-overlay"></div>

    <div class="main-container">
        <!-- Enlace preferencias -->
        <a href="preferencias.php" class="preferences-link">🎨 Preferencias</a>

        <!-- Header -->
        <div class="header">
            <h1>🎫 Gestor de Incidencias</h1>
            <div class="user-info">
                <span>Hola, <strong><?= especial(get_current_username()) ?></strong></span>
                <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="content">
            <!-- Mensajes -->
            <?= show_success() ?>
            <?= show_error() ?>

            <!-- Estadísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number stat-total"><?= especial($stats['total']) ?></div>
                    <div>Total Tickets</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number stat-abiertas"><?= especial($stats['abiertas']) ?></div>
                    <div>Abiertas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number stat-progreso"><?= especial($stats['en_progreso']) ?></div>
                    <div>En Progreso</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number stat-resueltas"><?= especial($stats['resueltas']) ?></div>
                    <div>Resueltas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number stat-cerradas"><?= especial($stats['cerradas']) ?></div>
                    <div>Cerradas</div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="actions">
                <a href="items_form.php" class="btn btn-success">➕ Nuevo Ticket</a>
                <a href="items_list.php" class="btn">📋 Ver Todos los Tickets</a>
            </div>

            <!-- Tickets recientes -->
            <div class="latest-tickets">
                <h2>📝 Tickets Recientes</h2>
                <?php if (empty($latest_tickets)): ?>
                    <p>No hay tickets creados todavía.</p>
                <?php else: ?>
                    <ul class="ticket-list">
                        <?php foreach ($latest_tickets as $ticket): ?>
                            <li class="ticket-item">
                                <div class="ticket-title">
                                    <a href="items_show.php?id=<?= especial($ticket['id']) ?>">
                                        <?= especial($ticket['titulo']) ?>
                                    </a>
                                </div>
                                <div class="ticket-meta">
                                    <span class="priority-badge priority-<?= especial($ticket['prioridad']) ?>">
                                        <?= ucfirst(especial($ticket['prioridad'])) ?>
                                    </span>
                                    <span><?= date('d/m/Y', strtotime($ticket['created_at'])) ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>