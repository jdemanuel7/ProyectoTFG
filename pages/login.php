<?php
// pages/login.php — versión que usa la función Conexion() para obtener el objeto mysqli

session_start();
require_once __DIR__ . '/../config/database.php';

// 1. Redirige si ya está logueado
if (!empty($_SESSION['user_id'])) {
    header('Location: /index.php');
    exit;
}

// 2. Genera token CSRF
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['csrf'];

$error = '';

// 3. Procesa POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $error = 'Petición no válida.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $pass  = $_POST['password'] ?? '';

        if ($email === '' || $pass === '') {
            $error = 'Email y contraseña son obligatorios.';
        } else {
            // Usa Conexion() SIN parámetros para obtener el objeto mysqli
            $conn = Conexion();
            if (!$conn) {
                die('No se pudo obtener la conexión a la BD.');
            }

            $stmt = $conn->prepare('SELECT id, password, is_admin FROM users WHERE email = ? LIMIT 1');
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($user = $result->fetch_assoc()) {
                if (password_verify($pass, $user['password'])) {
                    // 4. Autenticación correcta
                    $_SESSION['user_id']  = $user['id'];
                    $_SESSION['is_admin'] = (bool)$user['is_admin'];

                    session_regenerate_id(true); // mitiga fijación de sesión

                    $dest = $_SESSION['return_to'] ?? '/index.php';
                    unset($_SESSION['return_to']);
                    header('Location: ' . $dest);
                    exit;
                }
            }
            $error = 'Email o contraseña incorrectos.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<main class="container py-5" style="max-width: 400px;">
    <h1 class="mb-4">Iniciar sesión</h1>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($token) ?>">

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" required autofocus value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Entrar</button>
    </form>
    <p class="mt-3 text-center">
        ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>
    </p>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>