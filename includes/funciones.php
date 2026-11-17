<?php

declare(strict_types=1);

/**
 * Escapa una cadena para mostrarla de forma segura en HTML.
 *
 * Evita que el contenido introducido por los usuarios
 * pueda interpretarse como código HTML o JavaScript.
 *
 * @param string $texto Texto que se desea escapar.
 * @return string Texto escapado.
 */
function e(string $texto): string
{
    return htmlspecialchars(
        $texto,
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}