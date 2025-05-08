<?php
// pages/noticia_detalle.php - Vista detallada de una noticia
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
session_start();
include __DIR__ . '/../includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) {
    echo '<main class="container my-5"><div class="alert alert-danger">Noticia no válida.</div></main>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// Obtener noticia
$sql = "SELECT titulo, contenido, imagen, fecha_publicacion FROM noticias WHERE id = $id LIMIT 1";
$res = Conexion($sql);
if (!$res || mysqli_num_rows($res) === 0) {
    echo '<main class="container my-5"><div class="alert alert-warning">Noticia no encontrada.</div></main>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}
$noticia = mysqli_fetch_assoc($res);
?>
<main class="container my-5">
    <article class="card border-0 shadow-lg">
        <img src="/assets/img/news/<?= htmlspecialchars($noticia['imagen']) ?>" class="card-img-top" alt="Imagen de la noticia">
        <div class="card-body">
            <small class="text-muted d-block mb-2">
                Publicado el <?= date('d/m/Y', strtotime($noticia['fecha_publicacion'])) ?>
            </small>
            <h1 class="card-title h3 mb-4 text-primary fw-bold">
                <?= htmlspecialchars($noticia['titulo']) ?>
            </h1>
            <div class="card-text fs-5 lh-lg">
                <?= $noticia['contenido'] ?>
            </div>
        </div>
    </article>
    <div class="mt-4 text-end">
        <a href="/pages/noticias.php" class="btn btn-outline-secondary">Volver al listado</a>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>