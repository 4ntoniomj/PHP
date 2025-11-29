<?php
session_start();
require_once '../app/auth.php';
require_once '../app/utils.php';
require_once '../app/pdo.php';

require_login();

// Obtener preferencias visuales (Igual que en index.php)
$tamaño_actual = $_COOKIE['tamaño_letra'] ?? 'normal';
$color_fade_actual = $_COOKIE['color_fade'] ?? '#f5f0ff';
$fade_color = $color_fade_actual . 'e0'; // Agregar transparencia
$body_class = "body-$tamaño_actual";

$pdo = getPDO();

// --- LÓGICA DE BÚSQUEDA Y PAGINACIÓN --- //

$search = $_GET['search'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 6; // CAMBIADO: Ahora muestra 6 tickets por página
$offset = ($page - 1) * $per_page;

// Construir consulta base CON FILTRO DE USUARIO ACTUAL
$where = "WHERE usuario_id = ?";
$params = [get_current_user_id()]; 

if (!empty($search)) {
    $where .= " AND (titulo LIKE ? OR descripcion LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// 1. Obtener total de registros para paginación
$count_sql = "SELECT COUNT(*) as total FROM tickets $where";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_records = $stmt->fetch()['total'];
$total_pages = ceil($total_records / $per_page);

// 2. Obtener tickets paginados
$sql = "SELECT * FROM tickets $where ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Tickets - Gestor de Incidencias</title>
    <style>
        /* ESTILOS GENERALES (Copiados de index.php para consistencia) */
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

        /* FADE DINÁMICO */
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

        /* Header y Navegación */
        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 { color: #2c3e50; font-size: 1.5em; }
        
        .user-info { display: flex; align-items: center; gap: 1rem; font-size: 1em; }
        
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
            font-weight: 500; z-index: 10; transition: background 0.3s;
        }
        .preferences-link:hover { background: white; }

        .logout-btn {
            background: #e74c3c; color: white; padding: 0.5rem 1rem;
            text-decoration: none; border-radius: 8px; transition: all 0.3s ease;
            font-weight: 500;
        }
        .logout-btn:hover { background: #c0392b; transform: translateY(-2px); }

        /* ESTILOS ESPECÍFICOS DE LA LISTA (Adaptados) */
        .container {
            max-width: 1200px; margin: 2rem auto; padding: 0 2rem; flex: 1; width: 100%;
        }

        .search-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .search-form { display: flex; gap: 1rem; }
        
        .search-input {
            flex-grow: 1; padding: 0.75rem;
            border: 2px solid #e1e5e9; border-radius: 5px; font-size: 1em;
        }
        .search-input:focus { outline: none; border-color: #3498db; }

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

        /* TABLA DE TICKETS */
        .tickets-table {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .table-header {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            padding: 1rem 1.5rem;
            background: #34495e;
            color: white;
            font-weight: bold;
        }

        .ticket-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1fr;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #ecf0f1;
            align-items: center;
            transition: background 0.2s;
        }

        .ticket-row:hover { background: #f1f2f6; }
        .ticket-row:last-child { border-bottom: none; }

        .ticket-title a {
            color: #2c3e50; text-decoration: none; font-weight: 500;
        }
        .ticket-title a:hover { color: #3498db; text-decoration: underline; }

        /* BADGES */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.85em;
            font-weight: bold;
            text-align: center;
            display: inline-block;
        }

        /* Colores de Estado */
        .estado-abierta { background: #ffebee; color: #e74c3c; }
        .estado-en_progreso { background: #fff3e0; color: #f39c12; }
        .estado-resuelta { background: #e8f5e8; color: #27ae60; }
        .estado-cerrada { background: #f0f0f0; color: #7f8c8d; }

        /* Colores de Prioridad */
        .prioridad-alta { background: #ffebee; color: #e74c3c; }
        .prioridad-media { background: #fff3e0; color: #f39c12; }
        .prioridad-baja { background: #e8f5e8; color: #27ae60; }
        .prioridad-critica { background: #fce4ec; color: #c2185b; }

        /* ACCIONES */
        .actions { display: flex; gap: 0.5rem; }
        .action-btn {
            padding: 0.4rem 0.6rem; border: none; border-radius: 4px;
            cursor: pointer; text-decoration: none; font-size: 0.9em;
            transition: transform 0.2s;
        }
        .action-btn:hover { transform: translateY(-2px); }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
        .btn-view { background: #27ae60; color: white; }

        /* PAGINACIÓN MEJORADA */
        .pagination {
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            margin-top: 2rem; 
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .pagination-controls {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .page-info { 
            color: #2c3e50; 
            font-weight: 500; 
            background: rgba(255,255,255,0.8); 
            padding: 0.5rem 1rem; 
            border-radius: 20px;
            border: 1px solid #e1e5e9;
        }

        .page-btn {
            padding: 0.5rem 1rem;
            border: 1px solid #3498db;
            background: white;
            color: #3498db;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .page-btn:hover {
            background: #3498db;
            color: white;
            transform: translateY(-2px);
        }
        
        .page-btn.active {
            background: #3498db;
            color: white;
        }
        
        .page-btn.disabled {
            background: #f8f9fa;
            color: #6c757d;
            border-color: #dee2e6;
            cursor: not-allowed;
        }
        
        .page-btn.disabled:hover {
            background: #f8f9fa;
            color: #6c757d;
            transform: none;
        }

        .empty-state { 
            text-align: center; 
            padding: 3rem; 
            color: #7f8c8d; 
        }
        
        .empty-state h3 {
            margin-bottom: 1rem;
            font-size: 1.5em;
        }

        @media (max-width: 768px) {
            .header { flex-direction: column; gap: 1rem; padding: 1rem; }
            .user-info { flex-direction: column; gap: 1rem; }
            .search-form { flex-direction: column; }
            
            .table-header { display: none; } /* Ocultar cabecera en móvil */
            .ticket-row { 
                grid-template-columns: 1fr; 
                gap: 0.5rem;
                padding: 1.5rem;
                border-bottom: 2px solid #ddd;
            }
            
            .preferences-link { 
                position: relative; 
                top: auto; 
                right: auto; 
                align-self: flex-end; 
                margin: 0.5rem; 
            }
            
            .pagination {
                flex-direction: column;
                text-align: center;
            }
            
            .pagination-controls {
                flex-wrap: wrap;
                justify-content: center;
            }
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
                    <a href="papelera_unificada.php" style="color: #e74c3c;">🗑️ Papelera</a> 
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
                           placeholder="Buscar en títulos y descripciones..."
                           value="<?= especial($search) ?>">
                    <button type="submit" class="btn btn-search">🔍 Buscar</button>
                    <?php if (!empty($search)): ?>
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
                        <p><?= empty($search) ? 'Crea tu primer ticket para comenzar.' : 'No hay resultados para tu búsqueda.' ?></p>
                        <br>
                        <a href="items_form.php" class="btn btn-success">Crear Primer Ticket</a>
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
                                <a href="items_show.php?id=<?= especial($ticket['id']) ?>" class="action-btn btn-view" title="Ver detalle">👁️</a>
                                <a href="items_form.php?id=<?= especial($ticket['id']) ?>" class="action-btn btn-edit" title="Editar">✏️</a>
                                <a href="papelera_unificada.php?action=soft_delete&id=<?= especial($ticket['id']) ?>" 
                                   class="action-btn btn-delete"
                                   onclick="return confirm('¿Estás seguro de que quieres enviar este ticket a la papelera?')" title="Enviar a Papelera">🗑️</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <div class="page-info">
                        Mostrando <?= count($tickets) ?> de <?= $total_records ?> tickets
                        <?php if (!empty($search)): ?>
                            para "<strong><?= especial($search) ?></strong>"
                        <?php endif; ?>
                    </div>
                    
                    <div class="pagination-controls">
                        <?php if ($page > 1): ?>
                            <a href="?page=1&search=<?= especial($search) ?>" class="page-btn" title="Primera página">««</a>
                            <a href="?page=<?= $page - 1 ?>&search=<?= especial($search) ?>" class="page-btn" title="Página anterior">«</a>
                        <?php else: ?>
                            <span class="page-btn disabled">««</span>
                            <span class="page-btn disabled">«</span>
                        <?php endif; ?>

                        <?php
                        // Mostrar números de página (máximo 5 páginas alrededor de la actual)
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        for ($i = $start_page; $i <= $end_page; $i++): 
                        ?>
                            <a href="?page=<?= $i ?>&search=<?= especial($search) ?>" 
                               class="page-btn <?= $i == $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?= $page + 1 ?>&search=<?= especial($search) ?>" class="page-btn" title="Página siguiente">»</a>
                            <a href="?page=<?= $total_pages ?>&search=<?= especial($search) ?>" class="page-btn" title="Última página">»»</a>
                        <?php else: ?>
                            <span class="page-btn disabled">»</span>
                            <span class="page-btn disabled">»»</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <?php if ($total_records > 0): ?>
                    <div class="pagination">
                        <div class="page-info">
                            Mostrando <?= count($tickets) ?> tickets
                            <?php if (!empty($search)): ?>
                                para "<strong><?= especial($search) ?></strong>"
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>