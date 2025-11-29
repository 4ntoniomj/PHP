<?php
session_start();
require_once '../app/auth.php';
require_once '../app/pdo.php';
require_once '../app/utils.php';

require_login(); // Protege la ruta [cite: 27]

// 1. Obtener ID del ticket a borrar
$id = $_GET['id'] ?? null;
if (!$id) {
    redirect_with_message('items_list.php', 'ID no especificado.', true);
}

try {
    $pdo = getPDO();
    $current_user_id = get_current_user_id();

    // =================================================================
    // 🔴 INICIO DE LA TRANSACCIÓN (Requisito 10)
    // =================================================================
    // A partir de aquí, ninguna operación es definitiva hasta el commit.
    $pdo->beginTransaction(); // 

    // 2. Obtener datos del ticket ANTES de borrar (para guardarlos en auditoría)
    // También verificamos seguridad: ¿El ticket pertenece al usuario?
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $current_user_id]);
    $ticket = $stmt->fetch();

    if (!$ticket) {
        throw new Exception("El ticket no existe o no tienes permisos para borrarlo.");
    }

    // 3. Insertar en tabla AUDITORIA (Registro técnico con JSON)
    // Cumple con: "registra la acción en una tabla de auditoría" 
    $sqlAudit = "INSERT INTO auditoria (tabla_afectada, registro_id, accion, datos_anteriores, usuario_id) 
                 VALUES (:tabla, :id, :accion, :datos, :user)";
    
    $stmtAudit = $pdo->prepare($sqlAudit);
    $stmtAudit->execute([
        ':tabla'  => 'tickets',
        ':id'     => $id,
        ':accion' => 'DELETE',
        ':datos'  => json_encode($ticket), // Guardamos todo el estado anterior como JSON
        ':user'   => $current_user_id
    ]);

    // 4. Insertar en tabla ITEM_LOG (Tu tabla histórica personalizada)
    // Guardamos una copia legible de los datos principales
    $sqlLog = "INSERT INTO item_log (ticket_original_id, usuario_id, titulo, descripcion, prioridad, estado, created_at) 
               VALUES (:orig_id, :user_id, :titulo, :desc, :prio, :est, :fecha)";
    
    $stmtLog = $pdo->prepare($sqlLog);
    $stmtLog->execute([
        ':orig_id' => $ticket['id'],
        ':user_id' => $ticket['usuario_id'],
        ':titulo'  => $ticket['titulo'],
        ':desc'    => $ticket['descripcion'],
        ':prio'    => $ticket['prioridad'],
        ':est'     => $ticket['estado'],
        ':fecha'   => $ticket['created_at']
    ]);

    // 5. BORRAR el ticket de la tabla principal
    $stmtDelete = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
    $stmtDelete->execute([$id]);

    // =================================================================
    // 🧪 SIMULACIÓN DE FALLO (Para demostrar Rollback en la defensa)
    // =================================================================
    // Descomenta la línea de abajo para probar que los datos NO se borran si hay error.
    
    // throw new Exception("🔥 ERROR SIMULADO: Probando el Rollback automático.");

    // 6. CONFIRMAR CAMBIOS (COMMIT)
    // Si llegamos aquí sin errores, se guardan los logs y se borra el ticket.
    $pdo->commit(); 
    
    redirect_with_message('items_list.php', 'Ticket eliminado y auditado correctamente.');

} catch (Exception $e) {
    // =================================================================
    // 🔄 ROLLBACK (Requisito: "si ocurre un fallo, debe hacerse rollback") 
    // =================================================================
    // Deshace el Insert en Auditoria, el Insert en Log y el Delete en Tickets.
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    // Registrar el error en el log interno de PHP y avisar al usuario
    error_log("Error en transacción de borrado: " . $e->getMessage());
    redirect_with_message('items_list.php', 'Error al borrar: ' . $e->getMessage(), true);
}
?>