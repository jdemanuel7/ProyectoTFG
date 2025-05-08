<?php
/* config/database.php */

// conexión singleton
function Conexion(string $sql = null)
{
    static $mysqli;
    if (!$mysqli) {
        $mysqli = new mysqli('localhost', 'root', '', 'nbabd');
        $mysqli->set_charset('utf8mb4');
    }

    /* ---------- NUEVO ---------- */
    // Si no se pasa $sql, devolver el objeto mysqli
    if ($sql === null) {
        return $mysqli;
    }
    /* ---------------------------- */

    return $mysqli->query($sql);
}
