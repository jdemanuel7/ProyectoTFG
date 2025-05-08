<?php
// /admin/noticias_lista.php — listado y acciones rápidas sobre noticias usando Conexion()
require_once __DIR__ . '/../includes/auth.php';
require_admin();
require_once __DIR__ . '/../config/database.php';

// Obtener conexión mysqli
$conn = Conexion(); // sin argumentos → objeto mysqli

$ok = isset($_GET['ok']);

// ----------- Paginación -----------
$porPagina = 10;
$pagina    = max(1, (int)($_GET['p'] ?? 1));
$offset    = ($pagina - 1) * $porPagina;

// Total de registros
$totalRes = $conn->query('SELECT COUNT(*) AS total FROM noticias');
$total    = (int)($totalRes->fetch_assoc()['total'] ?? 0);
$paginas  = (int)ceil($total / $porPagina);

// Noticias de la página actual
$stmt = $conn->prepare(
    'SELECT id, titulo, fecha_publicacion
     FROM noticias
     ORDER BY fecha_publicacion DESC
     LIMIT ? OFFSET ?'
);
$stmt->bind_param('ii', $porPagina, $offset);
$stmt->execute();
$rs = $stmt->get_result();

include __DIR__ . '/../includes/header.php';
?>

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Noticias (<?= $total ?>)</h1>
        <a href="noticia_nueva.php" class="btn btn-success">+ Nueva noticia</a>
    </div>

    <?php if ($ok): ?>
        <div class="alert alert-success">Operación realizada con éxito.</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Fecha</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($n = $rs->fetch_assoc()): ?>
                    <tr>
                        <td><?= $n['id'] ?></td>
                        <td><?= htmlspecialchars($n['titulo']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($n['fecha_publicacion'])) ?></td>
                        <td class="text-end">
                            <a href="noticia_editar.php?id=<?= $n['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                            <a href="noticia_borrar.php?id=<?= $n['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Borrar definitivamente?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php if ($paginas > 1): ?>
        <nav aria-label="Paginación de noticias">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $paginas; $i++): ?>
                    <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="?p=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>