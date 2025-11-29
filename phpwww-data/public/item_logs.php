<?php
session_start();
require_once '../app/auth.php';
require_once '../app/utils.php';
require_once '../app/pdo.php';

require_login();

// Validar ID
$id = $_GET['id'] ?? null;
if (!$id) {
    redirect_with_message('items_list.php', 'No se especificó un ticket para eliminar.', true);
}

$pdo = getPDO();
$user_id = get_current_user_id();

try {
    // 1. INICIAR TRANSACCIÓN
    $pdo->beginTransaction();

    // 2. Obtener datos del ticket (y verificar que pertenezca al usuario)
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $user_id]);
    $ticket = $stmt->fetch();

    if (!$ticket) {
        $pdo->rollBack();
        redirect_with_message('items_list.php', 'Ticket no encontrado o no tienes permiso para eliminarlo.', true);
    }

    // 3. Insertar datos en la tabla de LOG (item_log)
    $sql_log = "INSERT INTO item_log (ticket_original_id, usuario_id, titulo, descripcion, prioridad, estado, created_at, deleted_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt_log = $pdo->prepare($sql_log);
    $stmt_log->execute([
        $ticket['id'],
        $ticket['usuario_id'],
        $ticket['titulo'],
        $ticket['descripcion'],
        $ticket['prioridad'],
        $ticket['estado'],
        $ticket['created_at'] // Usamos la fecha de creación original
    ]);

    // 4. Eliminar el ticket original (Soft Delete)
    $stmt_del = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt_del->execute([$id]);

    // 5. CONFIRMAR TRANSACCIÓN
    $pdo->commit();

    redirect_with_message('items_list.php', 'El ticket ha sido enviado a la papelera.');

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error al enviar ticket a papelera: " . $e->getMessage());
    redirect_with_message('items_list.php', 'Ocurrió un error en la base de datos al eliminar.', true);
}