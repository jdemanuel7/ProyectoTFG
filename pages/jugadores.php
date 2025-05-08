<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
include __DIR__ . '/../includes/header.php';

// Recoger parámetros de búsqueda
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$team   = isset($_GET['team'])   ? (int)$_GET['team'] : 0;

// Construir cláusulas WHERE
$where = [];
if ($search !== '') {
    // Escapamos caracteres especiales para LIKE
    $like = '%' . addslashes($search) . '%';
    $where[] = "j.nombre LIKE '{$like}'";
}
if ($team > 0) {
    $where[] = "j.equipo_id = {$team}";
}

// Montar la consulta
$sql = "
  SELECT 
    j.id,
    j.nombre AS jugador,
    e.id     AS equipo_id,
    e.nombre AS equipo,
    e.logo   AS equipo_logo
  FROM jugadores j
  JOIN equipos e ON j.equipo_id = e.id
";
if (count($where) > 0) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY j.nombre';

// Ejecutar la consulta con el string completo
$res = Conexion($sql);
?>
<main id="jugadores-page" class="container my-5">
    <div class="text-center mb-5">
        <h1 class="display-4">Jugadores NBA</h1>
        <p class="lead">Encuentra a tu jugador favorito y descubre su perfil.</p>
    </div>

    <!-- Formulario GET para búsqueda y filtro -->
    <form class="row mb-4 g-2 justify-content-center" method="get" action="jugadores.php">
        <div class="col-md-5">
            <input name="search"
                value="<?= htmlspecialchars($search) ?>"
                type="text"
                class="form-control"
                placeholder="Buscar jugador…">
        </div>
        <div class="col-md-4">
            <select name="team" class="form-select">
                <option value="0">Todos los equipos</option>
                <?php
                $eqs = Conexion("SELECT id, nombre FROM equipos ORDER BY nombre");
                while ($e = mysqli_fetch_assoc($eqs)): ?>
                    <option value="<?= $e['id'] ?>" <?= $e['id'] == $team ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['nombre']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">🔍</button>
        </div>
    </form>

    <!-- Grid de jugadores -->
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        <?php if ($res && mysqli_num_rows($res) > 0): ?>
            <?php while ($p = mysqli_fetch_assoc($res)): ?>
                <div class="col player-card">
                    <div class="card h-100 text-center p-3">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <?php if ($p['equipo_logo']): ?>
                                <img src="/assets/img/logos/<?= htmlspecialchars($p['equipo_logo']) ?>"
                                    alt="<?= htmlspecialchars($p['equipo']) ?>"
                                    width="60"
                                    class="mb-3 rounded-circle">
                            <?php endif; ?>
                            <h5 class="card-title mb-2"><?= htmlspecialchars($p['jugador']) ?></h5>
                            <p class="text-muted small mb-3"><?= htmlspecialchars($p['equipo']) ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="detalle_jugador.php?id=<?= $p['id'] ?>"
                                class="btn btn-sm btn-primary w-100 shadow">Ver perfil</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center text-warning">
                No se encontraron jugadores.
            </div>
        <?php endif; ?>
    </div>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>