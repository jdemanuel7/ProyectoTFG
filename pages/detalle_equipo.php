<?php
// pages/detalle_equipo.php — ficha de equipo sin fotos remotas, solo placeholder

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

$conn = Conexion();
$id   = (int)($_GET['id'] ?? 0);
if ($id <= 0) die(require __DIR__ . '/../includes/404.php');

// Datos del equipo
$stmt = $conn->prepare('SELECT id, nombre, abbreviation, logo, conference, wins, losses FROM equipos WHERE id=? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$equipo = $stmt->get_result()->fetch_assoc() ?: die(require __DIR__ . '/../includes/404.php');
$record = $equipo['wins'] . '‑' . $equipo['losses'];

// Favorito actual
$isFav = false;
if (!empty($_SESSION['user_id'])) {
    $stmtFav = $conn->prepare('SELECT favorite_team_id FROM users WHERE id=?');
    $stmtFav->bind_param('i', $_SESSION['user_id']);
    $stmtFav->execute();
    $isFav = ($stmtFav->get_result()->fetch_column() == $id);
}

// Quinteto inicial
$stmt = $conn->prepare('SELECT id, nombre FROM jugadores WHERE equipo_id=? ORDER BY nombre ASC LIMIT 5');
$stmt->bind_param('i', $id);
$stmt->execute();
$starters = $stmt->get_result();

// Plantilla completa
$stmt = $conn->prepare('SELECT id, nombre FROM jugadores WHERE equipo_id=? ORDER BY nombre ASC');
$stmt->bind_param('i', $id);
$stmt->execute();
$roster = $stmt->get_result();

include __DIR__ . '/../includes/header.php';
?>

<header class="team-hero mb-5 text-white" style="background:linear-gradient(90deg,#002B5C,#C8102E)">
    <div class="container py-5">
        <div class="d-flex align-items-center gap-3">
            <img src="/assets/img/logos/<?= $equipo['logo'] ?>" alt="<?= $equipo['abbreviation'] ?>" height="90">
            <div>
                <h1 class="display-5 mb-0"><?= htmlspecialchars($equipo['nombre']) ?></h1>
                <span class="badge bg-secondary me-1"><?= $equipo['conference'] ?></span>
                <span class="badge bg-success"><?= $record ?></span>
            </div>
        </div>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <form method="post" action="/pages/set_favorite.php" class="mt-3">
                <input type="hidden" name="team" value="<?= $id ?>">
                <button class="btn <?= $isFav ? 'btn-success' : 'btn-outline-light' ?> btn-sm">
                    <?= $isFav ? 'Favorito ✓' : 'Marcar como favorito' ?>
                </button>
            </form>
        <?php endif; ?>
    </div>
</header>

<div class="container mb-5">
    <h2 class="mb-3">Quinteto inicial</h2>
    <div class="row g-4 mb-5">
        <?php while ($p = $starters->fetch_assoc()): ?>
            <div class="col-6 col-md-4 col-lg-2 text-center">
                <img src="/assets/img/players/placeholder.jpg" class="rounded-circle mb-2" width="90" height="90" alt="<?= htmlspecialchars($p['nombre']) ?>">
                <div class="fw-bold"><?= htmlspecialchars($p['nombre']) ?></div>
            </div>
        <?php endwhile; ?>
    </div>

    <h2 class="mb-3">Plantilla completa</h2>
    <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Jugador</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($p = $roster->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nombre']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>