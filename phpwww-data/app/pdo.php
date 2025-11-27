<?php
function getPDO() {

    /* =====  CONFIGURAR CONEXIÓN PDO ====== */
    $host = 'bsd';
    $dbname = 'GDI';
    $user = 'root';
    $pass = '123456789';


    try {
        $pdo = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4", // utf8 para no tener problemas con acentos y signos
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // si algo falla en SQL, lanza excepcion
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, //nos devuelve arrays con clave->valor
                PDO::ATTR_EMULATE_PREPARES   => false //usa consultas SQL parametrizadas, para prevenir inyeccion de código SQL
            ]
        );
        return $pdo; 
    } catch (PDOException $e) {
        die("Error de conexión BD: " . $e->getMessage()); //Captura errores y los guarda en $e
    }
}
?>