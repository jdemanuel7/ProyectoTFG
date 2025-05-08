<?php
// index.php — portada dinámica con noticias desde la BD

session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
include __DIR__ . '/includes/header.php';

// --------------------------------------------------------
// OBTENER NOTICIAS (1 para hero + 4 adicionales)
// --------------------------------------------------------
$rsNoticias = obtenerNoticias(5);
$hero = $rsNoticias->fetch_assoc();
?>

<!-- NBA-style overrides -->
<link rel="stylesheet" href="/assets/css/index.css">

<main class="container-fluid px-0">

    <!-- BARRA SECUNDARIA -->
    <nav class="bg-gradient py-2 shadow-sm" style="background: linear-gradient(90deg, #17408B, #C8102E);">
        <div class="container d-flex flex-wrap justify-content-center justify-content-lg-end gap-3">
            <a href="https://www.nba.com/leaguepass" class="btn btn-sm btn-light text-dark px-3 shadow-sm d-flex align-items-center">
                <i class="bi bi-play-fill me-1"></i> League Pass
            </a>
            <a href="https://store.nba.com" class="btn btn-sm btn-light text-dark px-3 shadow-sm d-flex align-items-center">
                <i class="bi bi-cart-fill me-1"></i> NBA Store
            </a>
        </div>
    </nav>

    <!-- CARRUSEL DE PRÓXIMOS PARTIDOS -->
    <section class="upcoming-games bg-light py-4" aria-labelledby="upcoming-games-title">
        <div class="container">
            <h2 id="upcoming-games-title" class="visually-hidden">Próximos partidos</h2>
            <div class="d-flex overflow-auto gap-4">
                <?php
                $games = Conexion("SELECT fecha, local, visitante, puntos_local, puntos_visitante, status FROM partidos ORDER BY fecha ASC LIMIT 10");
                while ($g = mysqli_fetch_assoc($games)):
                    $dt = new DateTime($g['fecha']);
                    $label = strtoupper($dt->format('M d'));
                    $logoL = getLogo($g['local']);
                    $logoV = getLogo($g['visitante']);
                ?>
                    <article class="card text-center p-3 flex-shrink-0 shadow-sm" style="width:150px; border-radius:12px;">
                        <time class="small text-secondary mb-2 d-block"><?= $label ?></time>
                        <div class="d-flex justify-content-center align-items-center gap-2 mb-2">
                            <img src="<?= $logoL ?>" width="32" height="32" alt="<?= htmlspecialchars($g['local']) ?>">
                            <span class="fw-bold text-primary">vs</span>
                            <img src="<?= $logoV ?>" width="32" height="32" alt="<?= htmlspecialchars($g['visitante']) ?>">
                        </div>
                        <?php if ($g['status'] === 'Finalizado'): ?>
                            <div class="fw-bold text-danger"><?= (int)$g['puntos_local'] ?>–<?= (int)$g['puntos_visitante'] ?></div>
                        <?php else: ?>
                            <time class="small text-secondary"><?= $dt->format('H:i') ?></time>
                        <?php endif; ?>
                    </article>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <div class="container py-5">
        <div class="row">
            <!-- CONTENIDO PRINCIPAL -->
            <div class="col-lg-8 mb-4">
                <?php if ($hero): ?>
                    <section class="card mb-4 shadow-sm border-0 rounded">
                        <img src="/assets/img/news/<?= htmlspecialchars($hero['imagen']) ?>" class="card-img-top rounded-top" alt="<?= htmlspecialchars($hero['titulo']) ?>">
                        <div class="card-body">
                            <h2 class="card-title text-dark fs-4 fw-bold"><?= htmlspecialchars($hero['titulo']) ?></h2>
                            <p class="card-text text-muted"><?= htmlspecialchars($hero['excerpt']) ?></p>
                            <a href="/pages/noticia_detalle.php?id=<?= $hero['id'] ?>" class="stretched-link"></a>
                        </div>
                    </section>
                <?php endif; ?>

                <section class="mb-4" aria-labelledby="latest-news-title">
                    <h3 id="latest-news-title" class="h5 mb-3 text-primary">Últimas noticias</h3>
                    <div class="row g-3">
                        <?php while ($n = $rsNoticias->fetch_assoc()): ?>
                            <div class="col-6 col-md-3">
                                <article class="card news-card h-100 text-white shadow" style="background:linear-gradient(rgba(0,0,0,.5),rgba(0,0,0,.5)),url(/assets/img/news/<?= htmlspecialchars($n['imagen']) ?>);background-size:cover;">
                                    <a href="/pages/noticia_detalle.php?id=<?= $n['id'] ?>" class="stretched-link"></a>
                                    <div class="card-body d-flex flex-column justify-content-end">
                                        <small class="text-muted mb-1 d-block"><?= date('d M Y', strtotime($n['fecha_publicacion'])) ?></small>
                                        <h5 class="card-title mb-0 fs-6"><?= htmlspecialchars($n['titulo']) ?></h5>
                                    </div>
                                </article>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="text-end mt-3">
                        <a href="/pages/noticias.php" class="btn btn-sm btn-outline-secondary">
                            Ver todas las noticias →
                        </a>
                    </div>
                </section>
            </div>

            <!-- STANDINGS -->
            <aside class="col-lg-4">
                <?php foreach (['Este' => 'East', 'Oeste' => 'West'] as $label => $conf): ?>
                    <section class="card mb-4 shadow-sm">
                        <header class="card-header bg-white border-0">
                            <h5 class="mb-0 text-secondary">Conferencia <?= $label ?></h5>
                        </header>
                        <div class="card-body p-2">
                            <table class="table table-sm stats-table mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Equipo</th>
                                        <th>W</th>
                                        <th>L</th>
                                        <th>%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $rank = 1;
                                    $rs = Conexion("SELECT id,nombre,abbreviation,logo,wins,losses FROM equipos WHERE conference='$conf' ORDER BY (wins/(wins+losses)) DESC");
                                    while ($team = mysqli_fetch_assoc($rs)):
                                        $pos = $rank++;
                                        $pct = $team['wins'] ? round($team['wins'] / ($team['wins'] + $team['losses']), 3) : 0;
                                        $rowC = $pos <= 6 ? 'table-success' : ($pos <= 10 ? 'table-warning' : '');
                                    ?>
                                        <tr class="<?= $rowC ?>">
                                            <td><?= $pos ?></td>
                                            <td>
                                                <a href="/pages/detalle_equipo.php?id=<?= $team['id'] ?>" class="d-flex align-items-center text-reset text-decoration-none">
                                                    <img src="/assets/img/logos/<?= htmlspecialchars($team['logo']) ?>" width="24" height="24" class="me-1">
                                                    <?= htmlspecialchars($team['abbreviation']) ?>
                                                </a>
                                            </td>
                                            <td><?= (int)$team['wins'] ?></td>
                                            <td><?= (int)$team['losses'] ?></td>
                                            <td><?= number_format($pct, 3) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endforeach; ?>
            </aside>
        </div>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>