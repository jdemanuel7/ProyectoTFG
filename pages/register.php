<?php
// pages/register.php

session_start();
require_once __DIR__ . '/../config/database.php';
// Si ya está logueado, redirige a index
if (isset($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'] ?? '';

    // Validaciones básicas
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Introduce un email válido.';
    } elseif (strlen($password) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } elseif ($password !== $confirm) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        // Hashear y guardar usuario
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql  = "INSERT INTO users (email, password) VALUES ('" . addslashes($email) . "', '$hash')";
        if (Conexion($sql)) {
            header('Location: login.php');
            exit;
        } else {
            $error = 'Este email ya está registrado.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="card shadow-sm" style="width: 100%; max-width: 400px;">
        <div class="card-body p-4">
            <h2 class="card-title text-center mb-4">Crear una cuenta</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="" novalidate>
                <div class="form-floating mb-3">
                    <input type="email" id="email" name="email" class="form-control" placeholder="Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <label for="email">Email</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña" required>
                    <label for="password">Contraseña</label>
                </div>
                <div class="form-floating mb-4">
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Repetir Contraseña" required>
                    <label for="confirm_password">Repetir Contraseña</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Registrar</button>
            </form>
            <div class="text-center mt-3">
                <small>¿Ya tienes cuenta? <a href="login.php">Entrar aquí</a></small>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>