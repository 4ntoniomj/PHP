<?php
session_start();
require_once '../app/auth.php';
require_once '../app/pdo.php';
require_once '../app/utils.php';

require_login();

// Validar que recibimos un ID
$id = $_GET['id'] ?? null;

if (!$id) {
    redirect_with_message('items_list.php', 'ID de ticket no especificado', true);
}

try {
    $pdo = getPDO();
    $user_id = get_current_user_id();

    // --- INICIO DE LA TRANSACCIÓN ---
    // A partir de aquí, nada se guarda definitivamente hasta hacer commit()
    $pdo->beginTransaction(); 

    // 1. Verificar seguridad: ¿El ticket existe y pertenece al usuario?
    $stmt = $pdo->prepare("SELECT id, titulo FROM tickets WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$id, $user_id]);
    $ticket = $stmt->fetch();

    if (!$ticket) {
        // Si no es suyo, lanzamos excepción para ir al catch y hacer rollback (aunque no se haya cambiado nada aún)
        throw new Exception("No tienes permiso para eliminar este ticket o no existe.");
    }

    // 2. AUDITORÍA: Registrar quién está intentando borrar y qué [cite: 41]
    // Guardamos esto ANTES de borrar para que quede constancia en la transacción
    $accion = "Usuario ID $user_id eliminó el ticket ID $id: " . $ticket['titulo'];
    // Asegúrate de tener la tabla 'auditoria' creada en tu schema.sql
    $stmtAudit = $pdo->prepare("INSERT INTO auditoria (usuario_id, accion, fecha) VALUES (?, ?, NOW())");
    $stmtAudit->execute([$user_id, $accion]);

    // 3. BORRADO: Eliminar el ticket de la tabla
    $stmtDelete = $pdo->prepare("DELETE FROM tickets WHERE id = ?");
    $stmtDelete->execute([$id]);

    // --- SIMULACIÓN DE FALLO PARA PROBAR ROLLBACK  ---
    // Descomenta la siguiente línea para PROBAR que el rollback funciona.
    // Si lanzamos esta excepción, el ticket NO se borrará y la auditoría NO se guardará.
    
    // throw new Exception("⚠️ SIMULACIÓN DE FALLO: Probando Rollback automátic. Los datos no se borrarán.");


    // 4. CONFIRMAR CAMBIOS
    // Si todo ha ido bien y no ha saltado ninguna excepción, guardamos todo.
    $pdo->commit();

    redirect_with_message('items_list.php', 'Ticket eliminado correctamente (Transacción Exitosa).');

} catch (Exception $e) {
    // --- ROLLBACK [cite: 42] ---
    // Si algo falló (o simulamos el fallo), deshacemos TODOS los cambios pendientes.
    // Ni se borra el ticket, ni se inserta la auditoría.
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log("Error en transacción de borrado: " . $e->getMessage());
    redirect_with_message('items_list.php', 'Error al borrar: ' . $e->getMessage(), true);
}
?>