<?php
// pages/noticias.php - Listado de todas las noticias
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
session_start();
include __DIR__ . '/../includes/header.php';

// Obtener todas las noticias ordenadas por fecha descendente
$sql = "SELECT id, titulo, excerpt, imagen, fecha_publicacion FROM noticias ORDER BY fecha_publicacion DESC";
$rs = Conexion($sql);
?>
<main class="container py-5">
    <h1 class="mb-4 text-primary">Todas las noticias</h1>

    <?php if (mysqli_num_rows($rs) > 0): ?>
        <div class="row g-4">
            <?php while ($n = mysqli_fetch_assoc($rs)): ?>
                <div class="col-md-6 col-lg-4">
                    <article class="card h-100 shadow-sm border-0">
                        <img src="/assets/img/news/<?= htmlspecialchars($n['imagen']) ?>" class="card-img-top" alt="Imagen de <?= htmlspecialchars($n['titulo']) ?>">
                        <div class="card-body d-flex flex-column">
                            <small class="text-muted mb-2">
                                <?= date('d/m/Y', strtotime($n['fecha_publicacion'])) ?>
                            </small>
                            <h5 class="card-title"><?= htmlspecialchars($n['titulo']) ?></h5>
                            <p class="card-text text-muted flex-grow-1"><?= htmlspecialchars($n['excerpt']) ?></p>
                            <a href="/pages/noticia_detalle.php?id=<?= $n['id'] ?>" class="btn btn-outline-primary mt-3">Leer más</a>
                        </div>
                    </article>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No hay noticias disponibles en este momento.</div>
    <?php endif; ?>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>