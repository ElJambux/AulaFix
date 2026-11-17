<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../config/config.php';

$errores = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if (!validarTokenCsrf($token)) {
        $errores[] = 'Solicitud no válida. Recarga la página.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = 'Correo o contraseña incorrectos.';
    } else {
        $conexion = conectarBD();

        $consulta = $conexion->prepare(
            'SELECT id, nombre, email, password, rol
             FROM usuarios
             WHERE email = :email'
        );

        $consulta->execute(['email' => $email]);
        $usuario = $consulta->fetch();

        if (!$usuario || !password_verify($password, $usuario['password'])) {
            $errores[] = 'Correo o contraseña incorrectos.';
        } else {
            session_regenerate_id(true);

            $_SESSION['usuario'] = [
                'id' => $usuario['id'],
                'nombre' => $usuario['nombre'],
                'email' => $usuario['email'],
                'rol' => $usuario['rol']
            ];

            header('Location: /incidencias.php');
            exit;
        }
    }
}

$tituloPagina = 'Iniciar sesión';
require_once __DIR__ . '/../includes/cabecera.php';
?>

<section>
    <h1>Iniciar sesión</h1>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <p><?= e($_SESSION['mensaje']) ?></p>
        <?php unset($_SESSION['mensaje']); ?>
    <?php endif; ?>

    <?php foreach ($errores as $error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endforeach; ?>

    <form method="POST" action="/login.php">
        <input type="hidden" name="csrf_token" value="<?= e(obtenerTokenCsrf()) ?>">

        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" maxlength="150" value="<?= e($email) ?>" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Entrar</button>
    </form>
</section>

<?php require_once __DIR__ . '/../includes/pie.php'; ?>