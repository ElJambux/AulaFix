<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../includes/funciones.php';

$errores = [];

$titulo = '';
$descripcion = '';
$categoria = 'hardware';
$prioridad = 'media';

$categoriasPermitidas = [
    'hardware',
    'software',
    'red',
    'otro'
];

$prioridadesPermitidas = [
    'baja',
    'media',
    'alta'
];

// Procesamos el formulario cuando se envía.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $categoria = $_POST['categoria'] ?? '';
    $prioridad = $_POST['prioridad'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    // Comprobación CSRF.
    if (!is_string($token) || !validarTokenCsrf($token)) {
        $errores[] = 'La solicitud no es válida. Inténtalo de nuevo.';
    }

    // Validación del título.
    if ($titulo === '') {
        $errores[] = 'El título es obligatorio.';
    } elseif (mb_strlen($titulo) > 100) {
        $errores[] = 'El título no puede superar los 100 caracteres.';
    }

    // Validación de la descripción.
    if ($descripcion === '') {
        $errores[] = 'La descripción es obligatoria.';
    }

    // Validación de categoría y prioridad.
    if (!in_array($categoria, $categoriasPermitidas, true)) {
        $errores[] = 'La categoría seleccionada no es válida.';
    }

    if (!in_array($prioridad, $prioridadesPermitidas, true)) {
        $errores[] = 'La prioridad seleccionada no es válida.';
    }

    // Si todo es correcto, guardamos la incidencia.
    if (empty($errores)) {
        $_SESSION['incidencias'] ??= [];

        $_SESSION['incidencias'][] = [
            'id' => count($_SESSION['incidencias']) + 1,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'categoria' => $categoria,
            'prioridad' => $prioridad,
            'estado' => 'abierta',
            'fecha' => date('Y-m-d H:i:s')
        ];

        // Patrón Post/Redirect/Get.
        header('Location: /incidencias.php');
        exit;
    }
}

$tituloPagina = 'Nueva incidencia';

require_once __DIR__ . '/../includes/cabecera.php';

?>

<section>
    <h1>Registrar una incidencia</h1>

    <?php foreach ($errores as $error): ?>
        <p class="error">
            <?= e($error) ?>
        </p>
    <?php endforeach; ?>

    <form method="POST" action="/nueva.php">

        <input type="hidden" name="csrf_token" value="<?= e(obtenerTokenCsrf()) ?>">

        <label for="titulo">Título</label>
        <input type="text" id="titulo" name="titulo" maxlength="100" required value="<?= e($titulo) ?>">

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" maxlength="1000" required><?= e($descripcion) ?></textarea>
        <p id="contador-caracteres">0 / 1000 caracteres</p>
        <label for="categoria">Categoría</label>
        <select id="categoria" name="categoria" required>
            <option value="hardware" <?= $categoria === 'hardware' ? 'selected' : '' ?>>Hardware</option>
            <option value="software" <?= $categoria === 'software' ? 'selected' : '' ?>>Software</option>
            <option value="red" <?= $categoria === 'red' ? 'selected' : '' ?>>Red</option>
            <option value="otro" <?= $categoria === 'otro' ? 'selected' : '' ?>>Otro</option>
        </select>

        <label for="prioridad">Prioridad</label>
        <select id="prioridad" name="prioridad" required>
            <option value="baja" <?= $prioridad === 'baja' ? 'selected' : '' ?>>Baja</option>
            <option value="media" <?= $prioridad === 'media' ? 'selected' : '' ?>>Media</option>
            <option value="alta" <?= $prioridad === 'alta' ? 'selected' : '' ?>>Alta</option>
        </select>

        <button type="submit">Registrar incidencia</button>

    </form>
</section>

<script type="module" src="/js/formulario.js"></script>

<?php require_once __DIR__ . '/../includes/pie.php'; ?>