<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
session_start();
include __DIR__ . '/../includes/header.php';

// Obtener y validar ID del jugador
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) {
    echo '<main class="container my-5"><div class="alert alert-danger">Jugador no válido.</div></main>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// Consulta datos del jugador y su equipo con logo
$sql = sprintf(
    "SELECT j.id, j.nombre AS jugador, e.id AS equipo_id, e.nombre AS equipo, e.logo AS equipo_logo
     FROM jugadores j
     JOIN equipos e ON j.equipo_id = e.id
     WHERE j.id = %d
     LIMIT 1",
    $id
);
$res = Conexion($sql);

if (!$res || mysqli_num_rows($res) === 0) {
    echo '<main class="container my-5"><div class="alert alert-danger">Jugador no encontrado.</div></main>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}
$data = mysqli_fetch_assoc($res);
?>
<main class="container my-5">
    <div class="d-flex align-items-center mb-4">
        <h1 class="display-5 me-3"><?= htmlspecialchars($data['jugador']) ?></h1>
        <?php if (!empty($data['equipo_logo'])): ?>
            <a href="detalle_equipo.php?id=<?= $data['equipo_id'] ?>">
                <img src="/assets/img/logos/<?= htmlspecialchars($data['equipo_logo']) ?>"
                    alt="Logo del equipo"
                    width="60">
            </a>
        <?php endif; ?>
    </div>

    <div class="mb-4">
        <strong>Equipo:</strong>
        <?php if (!empty($data['equipo'])): ?>
            <a href="detalle_equipo.php?id=<?= $data['equipo_id'] ?>"
                class="text-decoration-none text-dark">
                <?= htmlspecialchars($data['equipo']) ?>
            </a>
        <?php else: ?>
            <span class="text-muted">No disponible</span>
        <?php endif; ?>
    </div>

    <!-- Mantenimiento de Estadísticas -->
    <section>
        <h2 class="mb-3">Estadísticas</h2>
        <div class="alert alert-info">
            Esta funcionalidad está actualmente en mantenimiento. Las estadísticas de los jugadores estarán disponibles próximamente.
        </div>
    </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>