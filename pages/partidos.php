<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../includes/header.php';
?>
<main class="container my-5">
    <h1 class="mb-4">Partidos NBA</h1>

    <!-- Próximos Partidos estilo tarjeta simple -->
    <section class="mb-5">
        <h2 class="h4 mb-3">Próximos Partidos</h2>
        <div class="row g-4">
            <?php
            $sql = "
        SELECT id, fecha, local, visitante
        FROM partidos
        WHERE status = 'Programado'
        ORDER BY fecha ASC
        LIMIT 8
      ";
            $res = Conexion($sql);
            if ($res && mysqli_num_rows($res) > 0):
                while ($match = mysqli_fetch_assoc($res)):
                    $dt       = new DateTime($match['fecha']);
                    $date     = $dt->format('d M');
                    $time     = $dt->format('H:i');
                    $logoHome = getLogo($match['local']);
                    $logoAway = getLogo($match['visitante']);
            ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body text-center">
                                <div class="mb-2 small text-muted"><?= strtoupper($date) ?></div>
                                <div class="d-flex justify-content-around align-items-center mb-2">
                                    <div>
                                        <img src="<?= $logoHome ?>" alt="<?= htmlspecialchars($match['local']) ?>" width="40">
                                        <div class="small mt-1"><?= htmlspecialchars($match['local']) ?></div>
                                    </div>
                                    <div class="fw-bold fs-4 mx-2">VS</div>
                                    <div>
                                        <img src="<?= $logoAway ?>" alt="<?= htmlspecialchars($match['visitante']) ?>" width="40">
                                        <div class="small mt-1"><?= htmlspecialchars($match['visitante']) ?></div>
                                    </div>
                                </div>
                                <div class="small text-secondary">Hora: <?= $time ?></div>
                            </div>
                            <div class="card-footer text-center bg-transparent">
                                <a href="detalle_partido.php?id=<?= $match['id'] ?>" class="btn btn-sm btn-primary">Ver detalles</a>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
            else:
                ?>
                <p class="text-warning">No hay partidos programados.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Últimos Resultados en grid de tarjetas -->
    <section>
        <h2 class="h4 mb-3">Últimos Resultados</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
            <?php
            $sql = "
        SELECT id, fecha, local, visitante, puntos_local, puntos_visitante
        FROM partidos
        WHERE status = 'Finalizado'
        ORDER BY fecha DESC
        LIMIT 9
      ";
            $res = Conexion($sql);
            if ($res && mysqli_num_rows($res) > 0):
                while ($row = mysqli_fetch_assoc($res)):
                    $dt       = new DateTime($row['fecha']);
                    $date     = $dt->format('d/m/Y');
                    $logoL    = getLogo($row['local']);
                    $logoV    = getLogo($row['visitante']);
                    $scoreL   = (int)$row['puntos_local'];
                    $scoreV   = (int)$row['puntos_visitante'];
            ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header bg-success text-center text-white small"><?= $date ?></div>
                            <div class="card-body d-flex justify-content-around align-items-center py-3">
                                <div class="text-center">
                                    <img src="<?= $logoL ?>" alt="<?= htmlspecialchars($row['local']) ?>" width="50">
                                    <div class="fw-semibold mt-1"><?= htmlspecialchars($row['local']) ?></div>
                                    <div class="display-6 fw-bold text-primary"><?= $scoreL ?></div>
                                </div>
                                <div class="fs-4 fw-bold">-</div>
                                <div class="text-center">
                                    <img src="<?= $logoV ?>" alt="<?= htmlspecialchars($row['visitante']) ?>" width="50">
                                    <div class="fw-semibold mt-1"><?= htmlspecialchars($row['visitante']) ?></div>
                                    <div class="display-6 fw-bold text-primary"><?= $scoreV ?></div>
                                </div>
                            </div>
                            <div class="card-footer text-center bg-transparent">
                                <a href="detalle_partido.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">Detalles</a>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
            else:
                ?>
                <p class="text-warning">No se encontraron resultados recientes.</p>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>