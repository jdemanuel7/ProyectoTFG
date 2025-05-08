<?php
// pages/equipos.php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../includes/header.php';
?>

<main class="container my-5">
    <h1 class="mb-4">Equipos NBA</h1>

    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-6 g-4">
        <?php
        $res = Conexion("SELECT id, nombre, logo FROM equipos ORDER BY nombre");
        if ($res && mysqli_num_rows($res) > 0):
            while ($team = mysqli_fetch_assoc($res)):
        ?>
                <div class="col">
                    <div class="card h-100 text-center shadow-sm hover-lift">
                        <?php if (!empty($team['logo'])): ?>
                            <img
                                src="/assets/img/logos/<?= htmlspecialchars($team['logo']) ?>"
                                alt="Logo <?= htmlspecialchars($team['nombre']) ?>"
                                class="card-img-top p-3"
                                style="height: 100px; object-fit: contain;"
                                onerror="this.onerror=null;this.src='/assets/img/logos/nba-logo.png';">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <h5 class="card-title mb-3"><?= htmlspecialchars($team['nombre']) ?></h5>
                            <a
                                href="detalle_equipo.php?id=<?= $team['id'] ?>"
                                class="btn btn-primary btn-sm mt-auto">
                                Ver equipo
                            </a>
                        </div>
                    </div>
                </div>
            <?php
            endwhile;
        else:
            ?>
            <div class="alert alert-warning">No hay equipos para mostrar.</div>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>