<?php

declare(strict_types=1);

/**
 * Página principal de AulaFix.
 *
 * @author ElJambux
 */

require_once __DIR__ . '/../includes/funciones.php';

$tituloPagina = 'Inicio';

require_once __DIR__ . '/../includes/cabecera.php';

?>

<section class="bienvenida">
    <h1>Bienvenido a AulaFix</h1>

    <p>
        La plataforma para registrar y gestionar
        las incidencias informáticas del aula.
    </p>
</section>

<?php require_once __DIR__ . '/../includes/pie.php'; ?>