<?php
// /admin/noticia_editar.php — muestra el formulario y procesa la edición
require_once __DIR__ . '/../includes/auth.php';
require_admin();
require_once __DIR__ . '/../config/database.php';

$conn = Conexion();
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    die('ID de noticia no válido.');
}

// --------------------------------------------------
// 1. Si viene POST -> procesar actualización
// --------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo    = trim($_POST['titulo']    ?? '');
    $excerpt   = trim($_POST['excerpt']   ?? '');
    $contenido = trim($_POST['contenido'] ?? '');

    if ($titulo === '' || $excerpt === '' || $contenido === '') {
        die('Faltan datos obligatorios.');
    }

    // Manejar imagen nueva (opcional)
    $updateImgSQL  = '';
    $updateImgBind = '';
    if (!empty($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $permitidas, true)) die('Formato de imagen no permitido.');

        // Asegurar directorio
        $dir = __DIR__ . '/../assets/img/news/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $nombreImagen = uniqid('news_', true) . '.' . $ext;
        $destino = $dir . $nombreImagen;
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $destino))
            die('No se pudo mover la nueva imagen.');

        $updateImgSQL  = ', imagen=?';
        $updateImgBind = $nombreImagen;
    }

    // Actualizar 
    $sql = "UPDATE noticias SET titulo=?, excerpt=?, contenido=?$updateImgSQL WHERE id=?";
    $stmt = $conn->prepare($sql);

    if ($updateImgSQL) {
        $stmt->bind_param('ssssi', $titulo, $excerpt, $contenido, $updateImgBind, $id);
    } else {
        $stmt->bind_param('sssi', $titulo, $excerpt, $contenido, $id);
    }
    $stmt->execute();

    header('Location: noticias_lista.php?ok=1');
    exit;
}

// --------------------------------------------------
// 2. GET -> mostrar formulario con datos actuales
// --------------------------------------------------
$stmt = $conn->prepare('SELECT titulo, excerpt, contenido, imagen FROM noticias WHERE id=? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$noticia = $stmt->get_result()->fetch_assoc();
if (!$noticia) die('Noticia no encontrada.');

include __DIR__ . '/../includes/header.php';
?>

<div class="container my-5" style="max-width: 720px;">
    <h1 class="mb-4">Editar noticia</h1>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Título</label>
            <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($noticia['titulo']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Extracto (máx. 160 car.)</label>
            <textarea name="excerpt" maxlength="160" rows="2" class="form-control" required><?= htmlspecialchars($noticia['excerpt']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Contenido</label>
            <textarea name="contenido" id="editor" rows="10" class="form-control" required><?= htmlspecialchars($noticia['contenido']) ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Imagen destacada (solo si deseas reemplazarla)</label><br>
            <img src="/assets/img/news/<?= htmlspecialchars($noticia['imagen']) ?>" alt="Imagen actual" style="max-width:150px" class="mb-2 d-block">
            <input type="file" name="imagen" accept="image/*" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Guardar cambios</button>
        <a href="noticias_lista.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#editor',
        height: 400,
        menubar: false
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>