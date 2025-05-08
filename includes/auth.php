<?php
// includes/auth.php — helper de autenticación y roles basado en tu esquema real
// Tabla `users`: id | email | password | favorite_team_id | role | created_at | is_admin

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

// Obtener el objeto mysqli usando tu función Conexion()
$conn = Conexion(); // Llamada sin argumentos -> objeto mysqli

// Cargar en $user la información mínima necesaria
$user = null;
if (!empty($_SESSION['user_id'])) {
    $stmt = $conn->prepare(
        'SELECT id, email, role, is_admin
         FROM users
         WHERE id = ? LIMIT 1'
    );
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc() ?: null;
}

// ----------------- Helpers de acceso -----------------
function require_login(): void
{
    if (empty($_SESSION['user_id'])) {
        $_SESSION['return_to'] = $_SERVER['REQUEST_URI'] ?? '/index.php';
        header('Location: /pages/login.php');
        exit;
    }
}

function require_admin(): void
{
    if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header('Location: /index.php');
        exit;
    }
}
