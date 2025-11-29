<?php
session_start();
require_once '../app/auth.php';
require_once '../app/utils.php';
require_once '../app/pdo.php';

require_login();

// Obtener preferencias visuales
$tamaño_actual = $_COOKIE['tamaño_letra'] ?? 'normal';
$color_fade_actual = $_COOKIE['color_fade'] ?? '#f5f0ff';
$fade_color = $color_fade_actual . 'e0'; 
$body_class = "body-$tamaño_actual";

$pdo = getPDO();

// --- LÓGICA DE BÚSQUEDA, FILTROS Y PAGINACIÓN --- //

// 1. Capturar parámetros de filtros
$search = $_GET['search'] ?? '';
$filter_prioridad = $_GET['filter_prioridad'] ?? '';
$filter_estado = $_GET['filter_estado'] ?? '';
// [NUEVO] Filtros de fecha
$date_start = $_GET['date_start'] ?? '';
$date_end = $_GET['date_end'] ?? '';

$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 6; 
$offset = ($page - 1) * $per_page;

// 2. Construir consulta base
$where = "WHERE usuario_id = ?";
$params = [get_current_user_id()]; 

// Filtro de Texto
if (!empty($search)) {
    $where .= " AND (titulo LIKE ? OR descripcion LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Filtro de Prioridad
if (!empty($filter_prioridad)) {
    $where .= " AND prioridad = ?";
    $params[] = $filter_prioridad;
}

// Filtro de Estado
if (!empty($filter_estado)) {
    $where .= " AND estado = ?";
    $params[] = $filter_estado;
}

// [NUEVO] Filtro de Fecha (Desde)
if (!empty($date_start)) {
    $where .= " AND created_at >= ?";
    // Añadimos la hora de inicio del día
    $params[] = $date_start . ' 00:00:00';
}

// [NUEVO] Filtro de Fecha (Hasta)
if (!empty($date_end)) {
    $where .= " AND created_at <= ?";
    // Añadimos la hora final del día
    $params[] = $date_end . ' 23:59:59';
}

// 3. Obtener total de registros (Count)
$count_sql = "SELECT COUNT(*) as total FROM tickets $where";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_records = $stmt->fetch()['total'];
$total_pages = ceil($total_records / $per_page);

// 4. Obtener tickets paginados
$sql = "SELECT * FROM tickets $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll();

// 5. Helper para mantener los filtros en la paginación
function get_page_url($page, $search, $prio, $est, $d_start, $d_end) {
    $query = http_build_query([
        'page' => $page,
        'search' => $search,
        'filter_prioridad' => $prio,
        'filter_estado' => $est,
        'date_start' => $d_start,
        'date_end' => $d_end
    ]);
    return "?" . $query;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tickets - Gestor de Incidencias</title>
    <style>
        /* ... (Tus estilos generales se mantienen igual) ... */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-image: url('https://cdn.pixabay.com/photo/2017/10/31/19/05/web-design-2906159_1280.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            min-height: 100vh;
            padding: 0;
            font-size: 16px;
        }
        
        body.body-pequeño { font-size: 14px; }
        body.body-normal { font-size: 16px; }
        body.body-grande { font-size: 18px; }
        body.body-muy-grande { font-size: 20px; }

        .fade-overlay {
            background-color: <?= $fade_color ?>;
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
        }

        .main-container {
            position: relative; z-index: 1; min-height: 100vh;
            display: flex; flex-direction: column;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex; justify-content: space-between; align-items: center;
        }
        .header h1 { color: #2c3e50; font-size: 1.5em; }
        .user-info { display: flex; align-items: center; gap: 1rem; }
        .nav-links { display: flex; gap: 1rem; }
        .nav-links a {
            color: #3498db; text-decoration: none; padding: 0.5rem 1rem;
            border-radius: 8px; transition: all 0.3s ease; font-weight: 500;
        }
        .nav-links a:hover { background: #ecf0f1; transform: translateY(-2px); }

        .preferences-link {
            position: absolute; top: 1rem; right: 2rem;
            background: rgba(255, 255, 255, 0.9); padding: 0.5rem 1rem;
            border-radius: 6px; text-decoration: none; color: #7c3aed;
            font-weight: 500; z-index: 10;
        }
        .preferences-link:hover { background: white; }

        .logout-btn {
            background: #e74c3c; color: white; padding: 0.5rem 1rem;
            text-decoration: none; border-radius: 8px; transition: all 0.3s ease; font-weight: 500;
        }
        .logout-btn:hover { background: #c0392b; }

        .container {
            max-width: 1200px; margin: 2rem auto; padding: 0 2rem; flex: 1; width: 100%;
        }

        /* --- ESTILOS DE BÚSQUEDA --- */
        .search-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .search-form { 
            display: flex; 
            gap: 1rem; 
            flex-wrap: wrap; 
            align-items: center;
        }
        
        .search-input, .search-select, .search-date {
            padding: 0.75rem;
            border: 2px solid #e1e5e9; 
            border-radius: 5px; 
            font-size: 1em;
            background-color: white;
        }

        .search-input { flex-grow: 2; min-width: 200px; }
        .search-select { flex-grow: 1; min-width: 140px; cursor: pointer; }
        /* [NUEVO] Estilo para fecha */
        .search-date { flex-grow: 1; min-width: 130px; cursor: pointer; color: #555; }

        .search-input:focus, .search-select:focus, .search-date:focus { 
            outline: none; border-color: #3498db; 
        }

        .label-date { font-size: 0.9em; color: #666; margin-right: -0.5rem; }

        .btn {
            padding: 0.75rem 1.5rem; border: none; border-radius: 5px;
            cursor: pointer; text-decoration: none; display: inline-block;
            text-align: center; transition: background 0.3s; color: white; font-size: 1em;
        }
        
        .btn-search { background: #3498db; }
        .btn-search:hover { background: #2980b9; }
        
        .btn-success { background: #27ae60; }
        .btn-success:hover { background: #219a52; }
        
        .btn-secondary { background: #95a5a6; }
        .btn-secondary:hover { background: #7f8c8d; }

        /* ... (Resto de estilos de tabla y badges igual) ... */
        .tickets-table {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); overflow: hidden;
        }
        .table-header {
            display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            padding: 1rem 1.5rem; background: #34495e; color: white; font-weight: bold;
        }
        .ticket-row {
            display: grid; grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            padding: 1rem 1.5rem; border-bottom: 1px solid #ecf0f1; align-items: center;
        }
        .ticket-row:hover { background: #f1f2f6; }
        .ticket-row:last-child { border-bottom: none; }
        .ticket-title a { color: #2c3e50; text-decoration: none; font-weight: 500; }
        .ticket-title a:hover { color: #3498db; text-decoration: underline; }

        .badge { padding: 0.25rem 0.75rem; border-radius: 15px; font-size: 0.85em; font-weight: bold; }
        
        /* Colores */
        .estado-abierta { background: #ffebee; color: #e74c3c; }
        .estado-en_progreso { background: #fff3e0; color: #f39c12; }
        .estado-resuelta { background: #e8f5e8; color: #27ae60; }
        .estado-cerrada { background: #f0f0f0; color: #7f8c8d; }

        .prioridad-alta { background: #ffebee; color: #e74c3c; }
        .prioridad-media { background: #fff3e0; color: #f39c12; }
        .prioridad-baja { background: #e8f5e8; color: #27ae60; }
        .prioridad-critica { background: #fce4ec; color: #c2185b; }

        .actions { display: flex; gap: 0.5rem; }
        .action-btn { padding: 0.4rem 0.6rem; border-radius: 4px; text-decoration: none; transition: transform 0.2s; }
        .action-btn:hover { transform: translateY(-2px); }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
        .btn-view { background: #27ae60; color: white; }

        /* Paginación */
        .pagination {
            display: flex; justify-content: space-between; align-items: center;
            margin: 2rem 0; flex-wrap: wrap; gap: 1rem;
        }
        .pagination-controls { display: flex; gap: 0.5rem; align-items: center; }
        .page-info { 
            color: #2c3e50; font-weight: 500; background: rgba(255,255,255,0.8); 
            padding: 0.5rem 1rem; border-radius: 20px; border: 1px solid #e1e5e9;
        }
        .page-btn {
            padding: 0.5rem 1rem; border: 1px solid #3498db; background: white;
            color: #3498db; text-decoration: none; border-radius: 5px; font-weight: 500;
        }
        .page-btn:hover { background: #3498db; color: white; }
        .page-btn.active { background: #3498db; color: white; }
        .page-btn.disabled { background: #f8f9fa; color: #6c757d; border-color: #dee2e6; cursor: not-allowed; }
        .page-btn.disabled:hover { background: #f8f9fa; color: #6c757d; }
        
        .empty-state { text-align: center; padding: 3rem; color: #7f8c8d; }

        @media (max-width: 768px) {
            .header { flex-direction: column; gap: 1rem; }
            .search-form { flex-direction: column; align-items: stretch; } /* Stack en móvil */
            .table-header { display: none; }
            .ticket-row { grid-template-columns: 1fr; gap: 0.5rem; padding: 1.5rem; border-bottom: 2px solid #ddd; }
            .pagination { flex-direction: column; text-align: center; }
            .label-date { display: none; } /* Ocultar etiquetas 'Desde/Hasta' en móvil para ahorrar espacio */
        }
    </style>
</head>
<body class="<?= $body_class ?>">
    
    <div class="fade-overlay"></div>

    <div class="main-container">
        <a href="preferencias.php" class="preferences-link">🎨 Preferencias</a>

        <div class="header">
            <h1>🎫 Lista de Tickets</h1>
            <div class="user-info">
                <div class="nav-links">
                    <a href="index.php">🏠 Inicio</a>
                    <a href="items_form.php">➕ Nuevo Ticket</a>
                    <a href="items_log.php" style="color: #e74c3c;">🗑️ Log</a> 
                </div>
                <span>Hola, <strong><?= especial($_SESSION['username']) ?></strong></span>
                <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
            </div>
        </div>

        <div class="container">
            <?= show_success() ?>
            <?= show_error() ?>

            <div class="search-section">
                <form method="GET" class="search-form">
                    
                    <input type="text" 
                           name="search" 
                           class="search-input" 
                           placeholder="Buscar..."
                           value="<?= especial($search) ?>">
                    
                    <select name="filter_prioridad" class="search-select">
                        <option value="">Prioridad</option>
                        <option value="baja" <?= $filter_prioridad == 'baja' ? 'selected' : '' ?>>Baja</option>
                        <option value="media" <?= $filter_prioridad == 'media' ? 'selected' : '' ?>>Media</option>
                        <option value="alta" <?= $filter_prioridad == 'alta' ? 'selected' : '' ?>>Alta</option>
                        <option value="critica" <?= $filter_prioridad == 'critica' ? 'selected' : '' ?>>Crítica</option>
                    </select>

                    <select name="filter_estado" class="search-select">
                        <option value="">Estado</option>
                        <option value="abierta" <?= $filter_estado == 'abierta' ? 'selected' : '' ?>>Abierta</option>
                        <option value="en_progreso" <?= $filter_estado == 'en_progreso' ? 'selected' : '' ?>>En Progreso</option>
                        <option value="resuelta" <?= $filter_estado == 'resuelta' ? 'selected' : '' ?>>Resuelta</option>
                        <option value="cerrada" <?= $filter_estado == 'cerrada' ? 'selected' : '' ?>>Cerrada</option>
                    </select>

                    <span class="label-date">Desde:</span>
                    <input type="date" name="date_start" class="search-date" value="<?= especial($date_start) ?>" title="Fecha Inicio">
                    
                    <span class="label-date">Hasta:</span>
                    <input type="date" name="date_end" class="search-date" value="<?= especial($date_end) ?>" title="Fecha Fin">

                    <button type="submit" class="btn btn-search">🔍 Filtrar</button>
                    
                    <?php if (!empty($search) || !empty($filter_prioridad) || !empty($filter_estado) || !empty($date_start) || !empty($date_end)): ?>
                        <a href="items_list.php" class="btn btn-secondary">❌ Limpiar</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="tickets-table">
                <div class="table-header">
                    <div>Título</div>
                    <div>Prioridad</div>
                    <div>Estado</div>
                    <div>Fecha</div>
                    <div>Acciones</div>
                </div>

                <?php if (empty($tickets)): ?>
                    <div class="empty-state">
                        <h3>📭 No se encontraron tickets</h3>
                        <p>No hay resultados para los filtros seleccionados.</p>
                        <br>
                        <a href="items_form.php" class="btn btn-success">Crear Ticket</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($tickets as $ticket): ?>
                        <div class="ticket-row">
                            <div class="ticket-title">
                                <a href="items_show.php?id=<?= especial($ticket['id']) ?>">
                                    <?= especial($ticket['titulo']) ?>
                                </a>
                            </div>
                            <div>
                                <span class="badge prioridad-<?= especial($ticket['prioridad']) ?>">
                                    <?= ucfirst(especial($ticket['prioridad'])) ?>
                                </span>
                            </div>
                            <div>
                                <span class="badge estado-<?= especial($ticket['estado']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', especial($ticket['estado']))) ?>
                                </span>
                            </div>
                            <div><?= date('d/m/Y', strtotime($ticket['created_at'])) ?></div>
                            <div class="actions">
                                <a href="items_show.php?id=<?= especial($ticket['id']) ?>" class="action-btn btn-view" title="Ver">👁️</a>
                                <a href="items_form.php?id=<?= especial($ticket['id']) ?>" class="action-btn btn-edit" title="Editar">✏️</a>
                                <a href="items_delete.php?id=<?= especial($ticket['id']) ?>" 
                                   class="action-btn btn-delete"
                                   onclick="return confirm('¿Enviar a LOG?')" title="Borrar">🗑️</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <div class="page-info">
                        Página <?= $page ?> de <?= $total_pages ?> (<?= $total_records ?> tickets)
                    </div>
                    
                    <div class="pagination-controls">
                        <?php 
                        // Variables para simplificar el HTML
                        $url_prev_1 = get_page_url(1, $search, $filter_prioridad, $filter_estado, $date_start, $date_end);
                        $url_prev = get_page_url($page - 1, $search, $filter_prioridad, $filter_estado, $date_start, $date_end);
                        $url_next = get_page_url($page + 1, $search, $filter_prioridad, $filter_estado, $date_start, $date_end);
                        $url_last = get_page_url($total_pages, $search, $filter_prioridad, $filter_estado, $date_start, $date_end);
                        ?>

                        <?php if ($page > 1): ?>
                            <a href="<?= $url_prev_1 ?>" class="page-btn">««</a>
                            <a href="<?= $url_prev ?>" class="page-btn">«</a>
                        <?php else: ?>
                            <span class="page-btn disabled">««</span>
                            <span class="page-btn disabled">«</span>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        for ($i = $start_page; $i <= $end_page; $i++): 
                        ?>
                            <a href="<?= get_page_url($i, $search, $filter_prioridad, $filter_estado, $date_start, $date_end) ?>" 
                               class="page-btn <?= $i == $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="<?= $url_next ?>" class="page-btn">»</a>
                            <a href="<?= $url_last ?>" class="page-btn">»»</a>
                        <?php else: ?>
                            <span class="page-btn disabled">»</span>
                            <span class="page-btn disabled">»»</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>