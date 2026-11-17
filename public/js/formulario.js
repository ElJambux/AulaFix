
'use strict';

const descripcion = document.getElementById('descripcion');
const contador = document.getElementById('contador-caracteres');

if (descripcion && contador) {
    const claveBorrador = 'aulafix_borrador_descripcion';

    // Recuperar el borrador guardado.
    const borrador = localStorage.getItem(claveBorrador);

    if (!descripcion.value && borrador) {
        descripcion.value = borrador;
    }

    /**
     * Actualiza el contador de caracteres.
     */
    function actualizarContador() {
        contador.textContent =
            `${descripcion.value.length} / 1000 caracteres`;
    }

    // Actualizar el contador al cargar la página.
    actualizarContador();

    // Guardar el borrador mientras se escribe.
    descripcion.addEventListener('input', () => {
        actualizarContador();
        localStorage.setItem(claveBorrador, descripcion.value);
    });
}
