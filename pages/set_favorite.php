<?php
// pages/set_favorite.php
session_start();
require_once __DIR__ . '/../config/database.php';

// Asegúrate de que el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/login.php');
    exit;
}

$uid = (int)$_SESSION['user_id'];
$tid = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($tid > 0) {
    // Actualiza el favorite_team_id en users
    Conexion("UPDATE users SET favorite_team_id = $tid WHERE id = $uid");
}

// Regresa a la página de detalle
header('Location: /pages/detalle_equipo.php?id=' . $tid);
exit;
