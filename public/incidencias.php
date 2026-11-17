<?php
declare(strict_types=1);

/**
 * Muestra el listado de incidencias registradas.
 *
 * En el Hito 1 los datos se almacenan temporalmente en la sesión.
 */

session_start();

require_once __DIR__ . '/../includes/funciones.php';

// Recuperamos las incidencias guardadas en la sesión.
$incidencias = $_SESSION['incidencias'] ?? [];

$datosJson = json_encode(
    $incidencias,
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
);

$tituloPagina = 'Incidencias';

require_once __DIR__ . '/../includes/cabecera.php';
?>

<section>
    <h1>Listado de incidencias</h1>

    <p>
        Aquí puedes consultar las incidencias registradas en el aula.
    </p>

    <p>
        <a href="/nueva.php">Registrar nueva incidencia</a>
    </p>

    <?php if (empty($incidencias)): ?>

        <p>Todavía no hay incidencias registradas.</p>

    <?php else: ?>

        <section aria-label="Resumen de incidencias">
            <div id="tarjetas-resumen"></div>
        </section>

        <nav id="pestanas-incidencias" aria-label="Filtrar por estado">
            <button type="button" data-pestana="todas">Todas</button>
            <button type="button" data-pestana="abierta">Abiertas</button>
            <button type="button" data-pestana="en_curso">En curso</button>
            <button type="button" data-pestana="resuelta">Resueltas</button>
        </nav>

        <div class="filtros">
            <label for="buscar-incidencia">Buscar:</label>
            <input type="search" id="buscar-incidencia" placeholder="Buscar por título...">

            <label for="filtro-categoria">Categoría:</label>
            <select id="filtro-categoria">
                <option value="">Todas</option>
                <option value="hardware">Hardware</option>
                <option value="software">Software</option>
                <option value="red">Red</option>
                <option value="otro">Otro</option>
            </select>

            <label for="filtro-prioridad">Prioridad:</label>
            <select id="filtro-prioridad">
                <option value="">Todas</option>
                <option value="baja">Baja</option>
                <option value="media">Media</option>
                <option value="alta">Alta</option>
            </select>

            <label for="filtro-estado">Estado:</label>
            <select id="filtro-estado">
                <option value="">Todos</option>
                <option value="abierta">Abierta</option>
                <option value="en_curso">En curso</option>
                <option value="resuelta">Resuelta</option>
            </select>
        </div>

        <table id="tabla-incidencias">
            <thead>
                <tr>
                    <th><button type="button" data-orden="id">ID</button></th>
                    <th><button type="button" data-orden="titulo">Título</button></th>
                    <th><button type="button" data-orden="categoria">Categoría</button></th>
                    <th><button type="button" data-orden="prioridad">Prioridad</button></th>
                    <th><button type="button" data-orden="estado">Estado</button></th>
                    <th><button type="button" data-orden="fecha">Fecha</button></th>
                </tr>
            </thead>

            <tbody id="cuerpo-incidencias">
                <?php foreach ($incidencias as $incidencia): ?>
                    <tr>
                        <td><?= e((string) $incidencia['id']) ?></td>
                        <td><?= e($incidencia['titulo']) ?></td>
                        <td><?= e($incidencia['categoria']) ?></td>
                        <td><?= e($incidencia['prioridad']) ?></td>
                        <td><?= e($incidencia['estado']) ?></td>
                        <td><?= e($incidencia['fecha']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>
    <script id="datos-incidencias" type="application/json">
<?= $datosJson ?>
</script>
</section>

<script type="module" src="/js/incidencias.js"></script>

<?php require_once __DIR__ . '/../includes/pie.php'; ?>