<?php

/**
 * Cabecera común de las páginas de AulaFix.
 *
 * @var string $tituloPagina Título de la página actual.
 */

$tituloPagina = $tituloPagina ?? 'AulaFix';

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= e($tituloPagina) ?> | AulaFix</title>

    <link rel="stylesheet" href="/css/estilos.css">
</head>

<body>

    <header class="cabecera">
        <div class="contenedor">
            <a href="/" class="logo">AulaFix</a>

            <nav>
                <a href="/">Inicio</a>
                <a href="/incidencias.php">Incidencias</a>
            </nav>
        </div>
    </header>

    <main class="contenedor"></main>