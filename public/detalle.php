<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: /login.php');
    exit;
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: /incidencias.php');
    exit;
}

$conexion = conectarBD();

$sql = 'SELECT i.*, u.nombre AS creador
        FROM incidencias i
        INNER JOIN usuarios u ON i.usuario_id = u.id
        WHERE i.id = :id';

$parametros = ['id' => $id];

if ($_SESSION['usuario']['rol'] !== 'profesor') {
    $sql .= ' AND i.usuario_id = :usuario_id';
    $parametros['usuario_id'] = $_SESSION['usuario']['id'];
}

$consulta = $conexion->prepare($sql);
$consulta->execute($parametros);
$incidencia = $consulta->fetch();
if (!$incidencia) {
    header('Location: /incidencias.php');
    exit;
}

/**
 * Comprueba si el usuario puede modificar la incidencia.
 */
$puedeModificar = $_SESSION['usuario']['rol'] === 'profesor';

$consultaHistorial = $conexion->prepare(
    'SELECT h.accion, h.detalle, h.fecha, u.nombre
     FROM historial h
     INNER JOIN usuarios u ON h.usuario_id = u.id
     WHERE h.incidencia_id = :id
     ORDER BY h.fecha DESC'
);

$consultaHistorial->execute(['id' => $id]);
$registrosHistorial = $consultaHistorial->fetchAll();

// Procesar el cambio de estado.
$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        $accion = $_POST['accion'] ?? 'estado';

        if (!$puedeModificar) {
            http_response_code(403);
            exit('No tienes permiso para realizar esta acción.');
        }

        if (!is_string($token) || !validarTokenCsrf($token)) {
            $errores[] = 'Solicitud no válida. Recarga la página.';
        } elseif ($accion === 'eliminar') {
            try {
                $conexion->beginTransaction();

                // Guardar el registro de eliminación.
                $historial = $conexion->prepare(
                    'INSERT INTO historial
                 (incidencia_id, usuario_id, accion, detalle)
                 VALUES (:id, :usuario_id, :accion, :detalle)'
                );

                $historial->execute([
                    'id' => $id,
                    'usuario_id' => $_SESSION['usuario']['id'],
                    'accion' => 'Eliminación de incidencia',
                    'detalle' => 'Incidencia eliminada: ' . $incidencia['titulo']
                ]);

                // El historial se conservará gracias a ON DELETE SET NULL.
                $eliminar = $conexion->prepare(
                    'DELETE FROM incidencias WHERE id = :id'
                );

                $eliminar->execute(['id' => $id]);

                $conexion->commit();

                header('Location: /incidencias.php');
                exit;
            } catch (PDOException $error) {
                if ($conexion->inTransaction()) {
                    $conexion->rollBack();
                }

                $errores[] = 'No se pudo eliminar la incidencia.';
            }
        } elseif ($accion === 'estado') {
            $nuevoEstado = $_POST['estado'] ?? '';
            $estadosPermitidos = ['abierta', 'en_curso', 'resuelta'];

            if (!in_array($nuevoEstado, $estadosPermitidos, true)) {
                $errores[] = 'El estado seleccionado no es válido.';
            } else {
                try {
                    $estadoAnterior = $incidencia['estado'];

                    if ($estadoAnterior !== $nuevoEstado) {
                        $conexion->beginTransaction();

                        $actualizar = $conexion->prepare(
                            'UPDATE incidencias
                         SET estado = :estado
                         WHERE id = :id'
                        );

                        $actualizar->execute([
                            'estado' => $nuevoEstado,
                            'id' => $id
                        ]);

                        $historial = $conexion->prepare(
                            'INSERT INTO historial
                         (incidencia_id, usuario_id, accion, detalle)
                         VALUES (:incidencia_id, :usuario_id, :accion, :detalle)'
                        );

                        $historial->execute([
                            'incidencia_id' => $id,
                            'usuario_id' => $_SESSION['usuario']['id'],
                            'accion' => 'Cambio de estado',
                            'detalle' => $estadoAnterior . ' → ' . $nuevoEstado
                        ]);

                        $conexion->commit();
                    }

                    header('Location: /detalle.php?id=' . $id);
                    exit;
                } catch (PDOException $error) {
                    if ($conexion->inTransaction()) {
                        $conexion->rollBack();
                    }

                    $errores[] = 'No se pudo actualizar el estado.';
                }
            }
        } else {
            $errores[] = 'Acción no válida.';
        }
    }


}


$tituloPagina = 'Detalle de incidencia';
require_once __DIR__ . '/../includes/cabecera.php';
?>

<section class="seccion">
    <h1><?= e($incidencia['titulo']) ?></h1>

    <p><strong>Descripción:</strong>
        <?= nl2br(e($incidencia['descripcion'])) ?>
    </p>

    <p><strong>Categoría:</strong>
        <?= e($incidencia['categoria']) ?>
    </p>

    <p><strong>Prioridad:</strong>
        <?= e($incidencia['prioridad']) ?>
    </p>

    <p>
        <strong>Estado:</strong>
        <?= e($incidencia['estado']) ?>
    </p>

    <?php foreach ($errores as $error): ?>
        <p role="alert"><?= e($error) ?></p>
    <?php endforeach; ?>

    <?php if ($puedeModificar): ?>
        <form method="POST" action="/detalle.php?id=<?= (int) $incidencia['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= e(obtenerTokenCsrf()) ?>">

            <label for="estado">Cambiar estado:</label>

            <select name="estado" id="estado">
                <option value="abierta" <?= $incidencia['estado'] === 'abierta' ? 'selected' : '' ?>>Abierta</option>

                <option value="en_curso" <?= $incidencia['estado'] === 'en_curso' ? 'selected' : '' ?>>En curso</option>

                <option value="resuelta" <?= $incidencia['estado'] === 'resuelta' ? 'selected' : '' ?>>Resuelta</option>
            </select>

            <button type="submit">Actualizar estado</button>
        </form>
        <form method="POST" action="/detalle.php?id=<?= (int) $incidencia['id'] ?>"
            onsubmit="return confirm('¿Seguro que quieres eliminar esta incidencia?');">

            <input type="hidden" name="csrf_token" value="<?= e(obtenerTokenCsrf()) ?>">

            <input type="hidden" name="accion" value="eliminar">

            <button type="submit">Eliminar incidencia</button>
        </form>
    <?php endif; ?>

    <p><strong>Creada por:</strong>
        <?= e($incidencia['creador']) ?>
    </p>

    <p><strong>Fecha:</strong>
        <?= e($incidencia['fecha_creacion']) ?>
    </p>

    <h2>Historial de cambios</h2>

    <?php if (empty($registrosHistorial)): ?>
        <p>No hay cambios registrados todavía.</p>
    <?php else: ?>
        <?php foreach ($registrosHistorial as $registro): ?>
            <article>
                <p>
                    <strong><?= e($registro['accion']) ?></strong>
                </p>
                <p><?= e($registro['detalle'] ?? '') ?></p>
                <p>Usuario: <?= e($registro['nombre']) ?></p>
                <p>Fecha: <?= e($registro['fecha']) ?></p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="/incidencias.php">Volver al listado</a>
</section>

<?php require_once __DIR__ . '/../includes/pie.php'; ?>