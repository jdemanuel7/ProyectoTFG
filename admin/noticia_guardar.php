<?php
// /admin/noticia_guardar.php — versión definitiva con autocreación de carpeta y Conexion()
require_once __DIR__ . '/../includes/auth.php';
require_admin();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: noticia_nueva.php');
    exit;
}

// 1. Validar campos obligatorios
$titulo    = trim($_POST['titulo']    ?? '');
$excerpt   = trim($_POST['excerpt']   ?? '');
$contenido = trim($_POST['contenido'] ?? '');

if ($titulo === '' || $excerpt === '' || $contenido === '') {
    die('Faltan datos obligatorios.');
}

// 2. Validar el fichero subido
if (empty($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
    die('Error subiendo la imagen.');
}

$ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
$permitidas = ['jpg', 'jpeg', 'png', 'webp'];
if (!in_array($ext, $permitidas, true)) {
    die('Formato de imagen no permitido.');
}

// 3. Asegurar el directorio destino
$uploadDir = __DIR__ . '/../assets/img/news/';
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        die('No se pudo crear el directorio de destino.');
    }
}

$nombreImagen = uniqid('news_', true) . '.' . $ext;
$destino      = $uploadDir . $nombreImagen;

if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
    die('No se pudo mover la imagen.');
}

// 4. Guardar en la base de datos
$conn = Conexion(); // objeto mysqli
$stmt = $conn->prepare(
    'INSERT INTO noticias (titulo, excerpt, contenido, imagen, fecha_publicacion) VALUES (?,?,?,?, NOW())'
);
$stmt->bind_param('ssss', $titulo, $excerpt, $contenido, $nombreImagen);
$stmt->execute();

// 5. Redirigir con mensaje de éxito
header('Location: noticias_lista.php?ok=1');
exit;
