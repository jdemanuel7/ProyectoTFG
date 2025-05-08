<?php
// pages/detalle_partido.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
session_start();
include __DIR__ . '/../includes/header.php';

// 1) Validar ID del partido
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id < 1) {
    echo '<div class="container my-5 alert alert-danger">Partido no válido.</div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// 2) Cargar datos básicos
$sql = "SELECT fecha, puntos_local, puntos_visitante, local, visitante FROM partidos WHERE id = $id LIMIT 1";
$res = Conexion($sql);
if (!$res || mysqli_num_rows($res) === 0) {
    echo '<div class="container my-5 alert alert-danger">Partido no encontrado.</div>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}
$partido = mysqli_fetch_assoc($res);
$jugado = ($partido['puntos_local'] !== null && $partido['puntos_visitante'] !== null);

// 3) Función segura para obtener datos del equipo
function obtenerEquipo($nombre)
{
    $conn = Conexion();
    $stmt = $conn->prepare("SELECT nombre, logo FROM equipos WHERE nombre = ? LIMIT 1");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    $res = $stmt->get_result();
    return ($res && $res->num_rows > 0)
        ? $res->fetch_assoc()
        : ['nombre' => $nombre, 'logo' => null];
}

$local     = obtenerEquipo($partido['local']);
$visitante = obtenerEquipo($partido['visitante']);
?>
<main class="container my-5">
    <div class="card shadow rounded-4 overflow-hidden">
        <!-- Encabezado: Equipos & Fecha -->
        <div class="bg-gradient p-4 text-white" style="background: linear-gradient(to right, #002B5C, #C8102E);">
            <div class="d-flex align-items-center justify-content-between">
                <!-- Equipo Local -->
                <div class="text-center">
                    <?php if ($local['logo']): ?>
                        <img src="/assets/img/logos/<?= htmlspecialchars($local['logo']) ?>" alt="Logo <?= htmlspecialchars($local['nombre']) ?>" height="70" class="bg-white p-2 rounded-circle shadow">
                    <?php endif; ?>
                    <div class="mt-2 fw-bold text-uppercase text-warning"><?= htmlspecialchars($local['nombre']) ?></div>
                </div>

                <!-- VS -->
                <div class="text-light fs-5 fw-bold">VS</div>

                <!-- Equipo Visitante -->
                <div class="text-center">
                    <?php if ($visitante['logo']): ?>
                        <img src="/assets/img/logos/<?= htmlspecialchars($visitante['logo']) ?>" alt="Logo <?= htmlspecialchars($visitante['nombre']) ?>" height="70" class="bg-white p-2 rounded-circle shadow">
                    <?php endif; ?>
                    <div class="mt-2 fw-bold text-uppercase text-warning"><?= htmlspecialchars($visitante['nombre']) ?></div>
                </div>

                <!-- Fecha -->
                <div class="text-end">
                    <span class="badge bg-light text-dark fs-6">
                        <?= (new DateTime($partido['fecha']))->format('d/m/Y H:i') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Marcador o estado -->
        <div class="p-4 bg-light text-center">
            <?php if ($jugado): ?>
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <div class="d-flex flex-column align-items-center">
                            <?php if ($local['logo']): ?>
                                <img src="/assets/img/logos/<?= htmlspecialchars($local['logo']) ?>" alt="Logo <?= htmlspecialchars($local['nombre']) ?>" width="50" class="mb-2">
                            <?php endif; ?>
                            <div class="display-4 fw-bold text-primary"><?= intval($partido['puntos_local']) ?></div>
                            <div class="text-uppercase fw-semibold text-dark"><?= htmlspecialchars($local['nombre']) ?></div>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                        <span class="badge bg-dark px-4 py-2">FINAL</span>
                    </div>
                    <div class="col-md-5">
                        <div class="d-flex flex-column align-items-center">
                            <?php if ($visitante['logo']): ?>
                                <img src="/assets/img/logos/<?= htmlspecialchars($visitante['logo']) ?>" alt="Logo <?= htmlspecialchars($visitante['nombre']) ?>" width="50" class="mb-2">
                            <?php endif; ?>
                            <div class="display-4 fw-bold text-danger"><?= intval($partido['puntos_visitante']) ?></div>
                            <div class="text-uppercase fw-semibold text-dark"><?= htmlspecialchars($visitante['nombre']) ?></div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">Este partido aún no se ha disputado.</div>
            <?php endif; ?>
        </div>

        <!-- Botón externo y reproductor -->
        <div class="bg-white p-4 text-center border-top">
            <a href="https://topstreams.info/nba/warriors" target="_blank" rel="noopener noreferrer" class="btn btn-primary rounded-pill px-5 py-2 fw-semibold">
                <i class="bi bi-play-circle me-2"></i>Ver Partido Externo
            </a>
        </div>

        <!-- Intento de embed -->
        <div class="ratio ratio-16x9 border-top">
            <iframe src="https://topstreams.info/nba/warriors"
                title="Reproductor de Partido"
                frameborder="0"
                allowfullscreen
                class="rounded-bottom border-0"></iframe>
        </div>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>