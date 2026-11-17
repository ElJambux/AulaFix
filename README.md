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
- Creación, consulta, edición y eliminación de incidencias.
- Clasificación por categoría, prioridad y estado.
- Cambio de estado de las incidencias por profesores.
- Historial de cambios.
- Filtros, búsqueda y ordenación.
- Validación de formularios.
- Protección CSRF.
- Contraseñas protegidas mediante Argon2id.
- Gestión de sesiones y permisos.

## Estructura del proyecto

```text
AulaFix/
├── config/
│   └── config.php
├── database/
│   └── esquema.sql
├── includes/
│   ├── cabecera.php
│   ├── db.php
│   ├── funciones.php
│   └── pie.php
├── public/
│   ├── css/
│   │   └── estilos.css
│   ├── js/
│   │   ├── app.js
│   │   ├── formulario.js
│   │   ├── incidencias.js
│   │   └── preferencias.js
│   ├── detalle.php
│   ├── incidencias.php
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── nueva.php
│   └── registro.php
├── docs/
├── .gitignore
└── README.md
```

## Requisitos

- PHP 8.3 o superior.
- MySQL 8.
- Extensiones PDO y PDO MySQL de PHP.
- Navegador web moderno.

## Instalación y ejecución en local

1. Clonar el repositorio:

```bash
git clone https://github.com/ElJambux/AulaFix.git
```

2. Entrar en la carpeta del proyecto:

```bash
cd AulaFix
```

3. Configurar los datos de conexión a MySQL en `config/config.php`.

4. Crear la base de datos importando el archivo `database/esquema.sql`.

5. Iniciar el servidor PHP desde la carpeta del proyecto:

```bash
php -S localhost:8000 -t public
```

6. Abrir la aplicación en el navegador:

```text
http://localhost:8000
```

## Base de datos

La aplicación utiliza las siguientes tablas:

- `usuarios`: almacena los datos de los usuarios y sus roles.
- `equipos`: almacena información de los equipos informáticos.
- `incidencias`: almacena las incidencias registradas.
- `historial`: registra las acciones realizadas sobre las incidencias.

## Seguridad

- Contraseñas almacenadas mediante `password_hash()` con Argon2id.
- Consultas preparadas mediante PDO.
- Protección contra CSRF.
- Escape de datos antes de mostrarlos en HTML.
- Control de acceso según el rol del usuario.
- Gestión segura de sesiones.

## Autor

Proyecto desarrollado para los módulos 0612 y 0613 de Desarrollo de Aplicaciones Web (DAW).

## Uso de herramientas de inteligencia artificial

Durante el desarrollo de AulaFix se ha utilizado ChatGPT como herramienta de apoyo para:

- Resolver dudas sobre PHP, JavaScript, MySQL y Git.
- Comprender y corregir errores durante el desarrollo.
- Recibir orientación sobre seguridad, validación de formularios y organización del proyecto.
- Revisar código y comprobar el funcionamiento de las distintas partes de la aplicación.

La herramienta se ha utilizado como apoyo al aprendizaje y al desarrollo, revisando y probando las soluciones antes de incorporarlas al proyecto.

El autor del proyecto es responsable del código desarrollado y debe conocer su funcionamiento para poder explicarlo y justificar las decisiones técnicas durante la defensa.
