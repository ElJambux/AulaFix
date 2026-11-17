<?php
declare(strict_types=1);

/**
 * Crea y devuelve la conexión con la base de datos AulaFix.
 *
 * @return PDO Conexión activa con MySQL.
 */
function conectarBD(): PDO
{
    $dsn = 'mysql:host=' . DB_HOST
        . ';dbname=' . DB_NAME
        . ';charset=' . DB_CHARSET;

    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    return new PDO(
        $dsn,
        DB_USER,
        DB_PASS,
        $opciones
    );
}
