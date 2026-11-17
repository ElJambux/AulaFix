# AulaFix

Aplicación web para la gestión y seguimiento de incidencias informáticas dentro del aula.

## Descripción

AulaFix permite registrar, consultar y gestionar incidencias relacionadas con los equipos informáticos del aula, facilitando la comunicación entre alumnos y profesores.

## Tecnologías utilizadas

- PHP 8.3
- MySQL 8
- PDO
- HTML5
- CSS3
- JavaScript vanilla
- Git y GitHub

## Funcionalidades

- Registro e inicio de sesión de usuarios.
- Gestión de roles: alumno y profesor.
- Creación y consulta de incidencias.
- Clasificación por categoría, prioridad y estado.
- Edición y resolución de incidencias según permisos.
- Historial de cambios.
- Filtros, búsqueda y ordenación.
- Validación de formularios y protección CSRF.

## Estructura del proyecto

```text
AulaFix/
├── config/
├── database/
├── includes/
├── public/
│   ├── css/
│   └── js/
├── docs/
├── .gitignore
└── README.md
```

## Requisitos

- PHP 8.3 o superior.
- MySQL 8.
- Extensiones PDO y PDO MySQL de PHP.
- Navegador web moderno.

## Ejecución en local

1. Configurar la conexión a MySQL en `config/config.php`.
2. Crear la base de datos importando `database/esquema.sql`.
3. Iniciar el servidor PHP desde la carpeta del proyecto:

```bash
php -S localhost:8000 -t public
