<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>AND-ONE</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Icon library -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- Custom styles -->
    <link rel="stylesheet" href="/assets/css/style.css" />
    <link rel="stylesheet" href="/assets/css/index.css" />
</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background: linear-gradient(90deg, #002B5C, #C9082A);">
            <div class="container">
                <a class="navbar-brand fw-bold" href="/index.php" style="letter-spacing: .15rem; font-size: 1.3rem;">
                    <i class="bi bi-basketball me-1"></i> AND-ONE
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Mostrar navegación">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNav">
                    <ul class="navbar-nav ms-auto align-items-center gap-2">
                        <li class="nav-item"><a class="nav-link" href="/index.php"><i class="bi bi-house-door-fill me-1"></i>Inicio</a></li>
                        <li class="nav-item"><a class="nav-link" href="/pages/mi_equipo.php"><i class="bi bi-star-fill me-1"></i>Mi equipo</a></li>
                        <li class="nav-item"><a class="nav-link" href="/pages/equipos.php"><i class="bi bi-people-fill me-1"></i>Equipos</a></li>
                        <li class="nav-item"><a class="nav-link" href="/pages/jugadores.php"><i class="bi bi-person-lines-fill me-1"></i>Jugadores</a></li>
                        <li class="nav-item"><a class="nav-link" href="/pages/partidos.php"><i class="bi bi-calendar-event me-1"></i>Partidos</a></li>
                        <?php if (!empty($_SESSION['is_admin'])): ?>
                            <li class="nav-item"><a class="nav-link" href="/admin/noticias_lista.php"><i class="bi bi-tools me-1"></i>Panel</a></li>
                        <?php endif; ?>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <li class="nav-item">
                                <button class="btn btn-sm btn-outline-light ms-2" data-bs-toggle="modal" data-bs-target="#loginModal">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Entrar
                                </button>
                            </li>
                        <?php else: ?>
                            <li class="nav-item ms-2">
                                <a href="/pages/logout.php" class="btn btn-sm btn-light">
                                    <i class="bi bi-box-arrow-right me-1"></i>Salir
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Login Modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="loginModalLabel">Iniciar sesión</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="/pages/login.php">
                            <div class="mb-3">
                                <label for="modal-email" class="form-label">Email</label>
                                <input type="email" id="modal-email" name="email" class="form-control" required autofocus />
                            </div>
                            <div class="mb-3">
                                <label for="modal-password" class="form-label">Contraseña</label>
                                <input type="password" id="modal-password" name="password" class="form-control" required />
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-door-open me-1"></i>Entrar
                            </button>
                        </form>
                    </div>
                    <div class="modal-footer border-0">
                        <p class="mb-0 w-100 text-center small">¿No tienes cuenta? <a href="/pages/register.php">Regístrate</a></p>
                    </div>
                </div>
            </div>
        </div>
    </header>