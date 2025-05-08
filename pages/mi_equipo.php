<?php
// pages/mi_equipo.php

define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';
require_once BASE_PATH . '/includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirigir si no está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/login.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

// Obtener datos del equipo favorito
$sqlTeam = "SELECT u.favorite_team_id, e.nombre, e.logo, e.wins, e.losses
    FROM users u
    JOIN equipos e ON u.favorite_team_id = e.id
    WHERE u.id = $userId
    LIMIT 1";
$resTeam = Conexion($sqlTeam);
if (!$resTeam || mysqli_num_rows($resTeam) === 0) {
    include BASE_PATH . '/includes/header.php';
    echo '<div class="container py-5"><div class="alert alert-warning">Aún no has marcado un equipo como favorito.</div></div>';
    include BASE_PATH . '/includes/footer.php';
    exit;
}
$team = mysqli_fetch_assoc($resTeam);
$teamId   = (int) $team['favorite_team_id'];
$teamName = htmlspecialchars($team['nombre']);
$teamLogo = '/assets/img/logos/' . htmlspecialchars($team['logo']);
$wins     = (int) $team['wins'];
$losses   = (int) $team['losses'];
$pct      = ($wins + $losses) ? round($wins / ($wins + $losses), 3) : 0;

// Obtener próximo partido
$sqlNext = "SELECT fecha, local, visitante
    FROM partidos
    WHERE (local = '" . addslashes($teamName) . "' OR visitante = '" . addslashes($teamName) . "')
      AND fecha > NOW()
    ORDER BY fecha ASC
    LIMIT 1";
$resNext = Conexion($sqlNext);
$nextGame = $resNext ? mysqli_fetch_assoc($resNext) : null;

// Obtener próximos 5 partidos
$sqlGames = "SELECT fecha, local, visitante, puntos_local, puntos_visitante, status
    FROM partidos
    WHERE local = '" . addslashes($teamName) . "' OR visitante = '" . addslashes($teamName) . "'
    ORDER BY fecha ASC
    LIMIT 5";
$games = Conexion($sqlGames);

// Datos de líderes (sustituir con consulta real si dispones)
$leaders = [
    ['cat' => 'PTS', 'player' => 'Jugador A', 'val' => '24.3'],
    ['cat' => 'REB', 'player' => 'Jugador B', 'val' => '10.1'],
    ['cat' => 'AST', 'player' => 'Jugador C', 'val' => '6.8'],
];

include BASE_PATH . '/includes/header.php';
?>
<main class="container py-5">
    <!-- Encabezado de equipo -->
    <div class="d-flex align-items-center mb-4">
        <img src="<?= $teamLogo ?>" alt="<?= $teamName ?>" width="60" height="60" class="me-3">
        <h1 class="h3 mb-0">Mi equipo: <?= $teamName ?></h1>
    </div>

    <!-- Estadísticas principales -->
    <div class="row text-center mb-5">
        <div class="col-md-4 mb-3">
            <div class="card p-3">
                <div class="h2"><?= $wins . '–' . $losses ?></div>
                <small>Victorias–Derrotas</small>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card p-3">
                <div class="h2"><?= number_format($pct, 3) ?></div>
                <small>PCT Temporada</small>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card p-3">
                <?php if ($nextGame): ?>
                    <?php $dt = new DateTime($nextGame['fecha']); ?>
                    <div class="h2"><?= $dt->format('d/m H:i') ?></div>
                <?php else: ?>
                    <div class="h2">–</div>
                <?php endif; ?>
                <small>Próximo Partido</small>
            </div>
        </div>
    </div>

    <!-- Próximos partidos -->
    <section class="mb-5">
        <h2 class="h5 mb-3">Próximos Partidos</h2>
        <div class="row row-cols-1 row-cols-md-2 g-3">
            <?php while ($g = mysqli_fetch_assoc($games)): ?>
                <?php $dt = new DateTime($g['fecha']); ?>
                <div class="col">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= $dt->format('d/m') ?></strong><br>
                                <small class="text-muted"><?= $dt->format('H:i') ?></small>
                            </div>
                            <div class="text-center">
                                <span class="fw-bold"><?= htmlspecialchars($g['local']) ?></span> vs <span class="fw-bold"><?= htmlspecialchars($g['visitante']) ?></span>
                            </div>
                            <div>
                                <?php if ($g['status'] === 'Finalizado'): ?>
                                    <span class="badge bg-danger"><?= (int)$g['puntos_local'] ?>–<?= (int)$g['puntos_visitante'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary text-white">Pendiente</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Líderes del equipo -->
    <section class="mb-5">
        <h2 class="h5 mb-3">Líderes</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Cat</th>
                    <th>Jugador</th>
                    <th>Valor</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leaders as $l): ?>
                    <tr>
                        <td><?= $l['cat'] ?></td>
                        <td><?= $l['player'] ?></td>
                        <td><?= $l['val'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

    <!-- Plantilla -->
    <section class="mb-5">
        <h2 class="h5 mb-3">Plantilla</h2>
        <ul class="list-group mb-4">
            <?php
            $rosterRes = Conexion("SELECT nombre FROM jugadores WHERE equipo_id = $teamId ORDER BY nombre");
            while ($p = mysqli_fetch_assoc($rosterRes)): ?>
                <li class="list-group-item"><?= htmlspecialchars($p['nombre']) ?></li>
            <?php endwhile; ?>
        </ul>
    </section>

    <!-- Noticias -->
    <section>
        <h2 class="h5 mb-3">Noticias de <?= $teamName ?></h2>
        <?php mostrarNoticias($teamId); ?>
    </section>
</main>

<?php include BASE_PATH . '/includes/footer.php'; ?>