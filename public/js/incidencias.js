'use strict';

import {
    guardarPreferencia,
    obtenerPreferencia
} from './preferencias.js';

const elementoDatos = document.getElementById('datos-incidencias');
const cuerpoTabla = document.getElementById('cuerpo-incidencias');

if (elementoDatos && cuerpoTabla) {
    let incidencias = [];

    try {
        incidencias = JSON.parse(elementoDatos.textContent);
    } catch (error) {
        console.error('Error al leer las incidencias:', error);
    }

    if (!Array.isArray(incidencias)) {
        incidencias = [];
    }

    const buscar = document.getElementById('buscar-incidencia');
    const categoria = document.getElementById('filtro-categoria');
    const prioridad = document.getElementById('filtro-prioridad');
    const estado = document.getElementById('filtro-estado');
    const tarjetas = document.getElementById('tarjetas-resumen');

    buscar.value = obtenerPreferencia('busqueda');
    categoria.value = obtenerPreferencia('categoria');
    prioridad.value = obtenerPreferencia('prioridad');
    estado.value = obtenerPreferencia('estado');

    const pestanasPermitidas = [
        'todas',
        'abierta',
        'en_curso',
        'resuelta'
    ];

    let pestanaActual = obtenerPreferencia('pestana', 'todas');

    if (!pestanasPermitidas.includes(pestanaActual)) {
        pestanaActual = 'todas';
    }

    let campoOrden = 'fecha';
    let ordenAscendente = false;

    /**
     * Calcula el tiempo transcurrido desde una fecha.
     *
     * @param {string} fecha Fecha de la incidencia.
     * @returns {string} Fecha relativa.
     */
    function obtenerFechaRelativa(fecha) {
        const fechaIncidencia = new Date(fecha.replace(' ', 'T'));

        if (Number.isNaN(fechaIncidencia.getTime())) {
            return fecha;
        }

        const minutos = Math.floor(
            (Date.now() - fechaIncidencia.getTime()) / 60000
        );

        if (minutos < 1 && minutos >= 0) return 'Ahora mismo';
        if (minutos < 60 && minutos >= 0) return `Hace ${minutos} min`;

        const horas = Math.floor(minutos / 60);

        if (horas < 24 && horas >= 0) return `Hace ${horas} h`;

        const dias = Math.floor(horas / 24);

        if (dias < 30 && dias >= 0) return `Hace ${dias} días`;

        return fechaIncidencia.toLocaleDateString('es-ES');
    }

    /**
     * Genera las tarjetas de resumen.
     */
    function mostrarTarjetas() {
        const total = incidencias.length;
        const abiertas = incidencias.filter(i => i.estado === 'abierta').length;
        const enCurso = incidencias.filter(i => i.estado === 'en_curso').length;
        const resueltas = incidencias.filter(i => i.estado === 'resuelta').length;

        tarjetas.replaceChildren();

        const datos = [
            ['Total', total],
            ['Abiertas', abiertas],
            ['En curso', enCurso],
            ['Resueltas', resueltas]
        ];

        datos.forEach(([nombre, cantidad]) => {
            const tarjeta = document.createElement('article');
            const titulo = document.createElement('h3');
            const numero = document.createElement('p');

            titulo.textContent = nombre;
            numero.textContent = cantidad;

            tarjeta.append(titulo, numero);
            tarjetas.appendChild(tarjeta);
        });
    }

    /**
     * Filtra, ordena y muestra las incidencias.
     */
    function mostrarIncidencias() {
        const texto = buscar.value.toLowerCase();

        const filtradas = incidencias.filter(incidencia => {
            return (
                incidencia.titulo.toLowerCase().includes(texto) &&
                (!categoria.value || incidencia.categoria === categoria.value) &&
                (!prioridad.value || incidencia.prioridad === prioridad.value) &&
                (!estado.value || incidencia.estado === estado.value) &&
                (pestanaActual === 'todas' || incidencia.estado === pestanaActual)
            );
        });

        const prioridades = {
            baja: 1,
            media: 2,
            alta: 3
        };

        filtradas.sort((a, b) => {
            let valorA = a[campoOrden];
            let valorB = b[campoOrden];

            if (campoOrden === 'prioridad') {
                valorA = prioridades[valorA] ?? 0;
                valorB = prioridades[valorB] ?? 0;
            } else if (campoOrden === 'id') {
                valorA = Number(valorA);
                valorB = Number(valorB);
            } else {
                valorA = String(valorA).toLowerCase();
                valorB = String(valorB).toLowerCase();
            }

            const resultado = valorA < valorB ? -1 : valorA > valorB ? 1 : 0;

            return ordenAscendente ? resultado : -resultado;
        });

        cuerpoTabla.replaceChildren();

        filtradas.forEach(incidencia => {
            const fila = document.createElement('tr');

            // Celda del identificador.
            const celdaId = document.createElement('td');
            celdaId.textContent = String(incidencia.id);

            // Celda del título con enlace al detalle.
            const celdaTitulo = document.createElement('td');
            const enlace = document.createElement('a');

            enlace.href = `/detalle.php?id=${encodeURIComponent(incidencia.id)}`;
            enlace.textContent = incidencia.titulo;

            celdaTitulo.appendChild(enlace);

            // Resto de celdas.
            const valores = [
                incidencia.categoria,
                incidencia.prioridad,
                incidencia.estado,
                obtenerFechaRelativa(incidencia.fecha)
            ];

            fila.appendChild(celdaId);
            fila.appendChild(celdaTitulo);

            valores.forEach(valor => {
                const celda = document.createElement('td');
                celda.textContent = String(valor ?? '');
                fila.appendChild(celda);
            });

            cuerpoTabla.appendChild(fila);
        });
    }

    // Eventos de los filtros.
    [buscar, categoria, prioridad, estado].forEach(control => {
        control.addEventListener('input', () => {
            guardarPreferencia('busqueda', buscar.value);
            guardarPreferencia('categoria', categoria.value);
            guardarPreferencia('prioridad', prioridad.value);
            guardarPreferencia('estado', estado.value);

            mostrarIncidencias();
        });
    });

    // Eventos de ordenación.
    document.querySelectorAll('[data-orden]').forEach(boton => {
        boton.addEventListener('click', () => {
            const nuevoCampo = boton.dataset.orden;

            if (campoOrden === nuevoCampo) {
                ordenAscendente = !ordenAscendente;
            } else {
                campoOrden = nuevoCampo;
                ordenAscendente = true;
            }

            mostrarIncidencias();
        });
    });

    // Eventos de las pestañas.
    document.querySelectorAll('[data-pestana]').forEach(boton => {
        boton.addEventListener('click', () => {
            pestanaActual = boton.dataset.pestana;

            guardarPreferencia('pestana', pestanaActual);

            mostrarIncidencias();
        });
    });

    mostrarTarjetas();
    mostrarIncidencias();
}
