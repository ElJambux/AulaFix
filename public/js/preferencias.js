
'use strict';

/**
 * Guarda una preferencia en localStorage.
 *
 * @param {string} clave Nombre de la preferencia.
 * @param {string} valor Valor que se guarda.
 */
export function guardarPreferencia(clave, valor) {
    localStorage.setItem(clave, valor);
}

/**
 * Recupera una preferencia de localStorage.
 *
 * @param {string} clave Nombre de la preferencia.
 * @param {string} valorPorDefecto Valor alternativo.
 * @returns {string} Preferencia guardada o valor alternativo.
 */
export function obtenerPreferencia(clave, valorPorDefecto = '') {
    return localStorage.getItem(clave) ?? valorPorDefecto;
}
