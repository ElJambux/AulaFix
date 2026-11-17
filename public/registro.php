<?php
declare(strict_types=1);

session_start();

require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../config/config.php';

$errores = [];
$nombre = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    if (!validarTokenCsrf($token)) {
        $errores[] = 'Solicitud no válida. Recarga la página.';
    }

    if ($nombre === '' || mb_strlen($nombre) > 100) {
        $errores[] = 'El nombre es obligatorio y no puede superar 100 caracteres.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 150) {
        $errores[] = 'Introduce un correo electrónico válido.';
    }

    if (strlen($password) < 8) {
        $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    }

    if (empty($errores)) {
        $conexion = conectarBD();

        $consulta = $conexion->prepare(
            'SELECT id FROM usuarios WHERE email = :email'
        );
        $consulta->execute(['email' => $email]);

        if ($consulta->fetch()) {
            $errores[] = 'Ese correo ya está registrado.';
        } else {
            $hash = password_hash($password, PASSWORD_ARGON2ID);

            $consulta = $conexion->prepare(
                'INSERT INTO usuarios (nombre, email, password, rol)
                 VALUES (:nombre, :email, :password, :rol)'
            );

            $consulta->execute([
                'nombre' => $nombre,
                'email' => $email,
                'password' => $hash,
                'rol' => 'alumno'
            ]);

            $_SESSION['mensaje'] = 'Registro completado. Ya puedes iniciar sesión.';
            header('Location: /login.php');
            exit;
        }
    }
}

$tituloPagina = 'Crear cuenta';
require_once __DIR__ . '/../includes/cabecera.php';
?>

<section>
    <h1>Crear cuenta</h1>

    <?php foreach ($errores as $error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endforeach; ?>

    <form method="POST" action="/registro.php">
        <input type="hidden" name="csrf_token" value="<?= e(obtenerTokenCsrf()) ?>">

        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" maxlength="100" value="<?= e($nombre) ?>" required>

        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" maxlength="150" value="<?= e($email) ?>" required>

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" minlength="8" required>

        <button type="submit">Registrarme</button>
    </form>
</section>

<?php require_once __DIR__ . '/../includes/pie.php'; ?>