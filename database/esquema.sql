
CREATE DATABASE IF NOT EXISTS aulafix
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE aulafix;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('alumno', 'profesor') NOT NULL DEFAULT 'alumno',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE equipos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) DEFAULT NULL
);

CREATE TABLE incidencias (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    equipo_id INT UNSIGNED DEFAULT NULL,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    categoria ENUM('hardware', 'software', 'red', 'otro') NOT NULL,
    prioridad ENUM('baja', 'media', 'alta') NOT NULL DEFAULT 'media',
    estado ENUM('abierta', 'en_curso', 'resuelta') NOT NULL DEFAULT 'abierta',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_incidencias_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id),

    CONSTRAINT fk_incidencias_equipo
        FOREIGN KEY (equipo_id) REFERENCES equipos(id)
        ON DELETE SET NULL
);

CREATE TABLE historial (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
   incidencia_id INT UNSIGNED DEFAULT NULL,
    usuario_id INT UNSIGNED NOT NULL,
    accion VARCHAR(100) NOT NULL,
    detalle TEXT DEFAULT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_historial_incidencia
        FOREIGN KEY (incidencia_id) REFERENCES incidencias(id),
    ON DELETE SET NULL
    CONSTRAINT fk_historial_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
