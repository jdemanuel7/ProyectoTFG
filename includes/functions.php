<?php
require_once __DIR__ . '/../config/database.php';

/* =============================================================
 | 1. Logos de equipos
 ============================================================= */
function getLogo(string $equipo): string
{
    $dir  = __DIR__ . '/../assets/img/logos/';
    $slug = strtolower(preg_replace('/[\s\.]+/', '_', $equipo));
    $candidatos = glob($dir . $slug . '*.{png,jpg,jpeg}', GLOB_BRACE);
    $filename   = $candidatos[0] ?? 'nba-logo.png';
    return '/assets/img/logos/' . basename($filename);
}

/* =============================================================
 | 2. Helper para obtener la conexión (mysqli)
 ============================================================= */
function db(): mysqli
{
    // Se asume que Conexion() sin argumentos devuelve mysqli
    return Conexion();
}

/* =============================================================
 | 3. NOTICIAS – CRUD y helpers
 ============================================================= */

/**
 * Devuelve las últimas $limit noticias (mysqli_result)
 */
function obtenerNoticias(int $limit = 4)
{
    $conn = db();
    $stmt = $conn->prepare(
        'SELECT id, titulo, excerpt, imagen, fecha_publicacion
         FROM noticias
         ORDER BY fecha_publicacion DESC
         LIMIT ?'
    );
    $stmt->bind_param('i', $limit);
    $stmt->execute();
    return $stmt->get_result();
}

/**
 * Pinta las noticias como tarjetas. Muestra un aviso si no hay.
 */
function mostrarNoticias(int $limit = 4): void
{
    $rs = obtenerNoticias($limit);

    if (!$rs || $rs->num_rows === 0) {
        echo '<p class="text-warning">Todavía no hay noticias publicadas.</p>';
        return;
    }

    echo '<div class="row row-cols-1 row-cols-md-' . min(3, $limit) . ' g-4">';
    while ($n = $rs->fetch_assoc()) {
        $img = '/assets/img/news/' . htmlspecialchars($n['imagen'] ?? 'placeholder.jpg');
        echo '<div class="col">';
        echo '  <article class="card news-card text-white shadow" style="background:linear-gradient(rgba(0,0,0,.6),rgba(0,0,0,.6)),url(' . $img . ');background-size:cover;height:250px;">';
        echo '    <a href="/pages/noticia_detalle.php?id=' . $n['id'] . '" class="stretched-link"></a>';
        echo '    <div class="card-body d-flex flex-column justify-content-end">';
        echo '      <small class="text-muted">' . date('d M Y', strtotime($n['fecha_publicacion'])) . '</small>';
        echo '      <h5 class="card-title">' . htmlspecialchars($n['titulo']) . '</h5>';
        echo '      <p class="card-text">' . htmlspecialchars($n['excerpt']) . '</p>';
        echo '    </div>';
        echo '  </article>';
        echo '</div>';
    }
    echo '</div>';
}

/* =============================================================
 | 4. Partidos destacados y últimos resultados (a implementar)
 ============================================================= */
function mostrarPartidosDestacados(int $limit = 10): void
{
    // Mantén tu lógica original aquí
}

function mostrarUltimosResultados(int $limit = 10): void
{
    // Mantén tu lógica original aquí
}
