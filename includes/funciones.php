<?php

declare(strict_types=1);

/**
 * Escapa una cadena para mostrarla de forma segura en HTML.
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

/**
 * Genera o recupera el token CSRF de la sesión.
 *
 * @return string Token de seguridad.
 */
function obtenerTokenCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Comprueba que el token recibido sea válido.
 *
 * @param string $token Token recibido del formulario.
 * @return bool Resultado de la comprobación.
 */
function validarTokenCsrf(string $token): bool
{
    return isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}
