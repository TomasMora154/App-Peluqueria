// Inicializa el paso actual y define los límites de pasos
let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

// Espera a que el DOM esté completamente cargado para iniciar la aplicación
document.addEventListener('DOMContentLoaded', function() {
    iniciarApp(); // Inicia la aplicación
})

// Función principal para iniciar la aplicación
function iniciarApp() {
    mostrarSeccion(); // Muestra y oculta las secciones
    tabs(); // Cambia la sección cuando se presionen los tabs
    botonesPaginador(); // Agrega o quita los botones del paginador
    paginaSiguiente();
    paginaAnterior();
}

// Función para mostrar la sección correspondiente al paso actual
function mostrarSeccion() {

    // Ocultar la sección que tenga la clase de mostrar
    const seccionAnterior = document.querySelector('.mostrar');
    if (seccionAnterior) {
        seccionAnterior.classList.remove('mostrar');
    }

    // Seleccionar la sección con el paso
    const pasoSelector = `#paso-${paso}`;
    const seccion = document.querySelector(pasoSelector);
    seccion.classList.add('mostrar');

    // Quita la clase de actual al tab anterior
    const tabAnterior = document.querySelector('.actual');
    if (tabAnterior) {
        tabAnterior.classList.remove('actual');
    }

    // Resaltar el tab actual
    const tab = document.querySelector(`[data-paso="${paso}"]`);
    tab.classList.add('actual');
}

function tabs() {
    // Agrega y cambia la variable según el tab seleccionado
    const botones = document.querySelectorAll('.tabs button');
    botones.forEach( boton => {
        // Agrega un evento click a cada botón de tab
        boton.addEventListener('click', function(e) {
            paso = parseInt(e.target.dataset.paso); // Actualiza el paso basado en el botón clicado
            mostrarSeccion();
            botonesPaginador();
        });
    })
}

// Función para mostrar/ocultar los botones de paginación
function botonesPaginador() {
    const paginaAnterior = document.querySelector('#anterior');
    const paginaSiguiente = document.querySelector('#siguiente');
    // Controla la visibilidad de los botones de paginación
    if (paso === 1) {
        paginaAnterior.classList.add('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    } else if (paso === 3) {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.add('ocultar');
    } else {
        paginaAnterior.classList.remove('ocultar');
        paginaSiguiente.classList.remove('ocultar');
    }
    mostrarSeccion(); // Asegura que la sección correcta esté visible
}

// Configura el evento para el botón de página anterior
function paginaAnterior() {
    const paginaAnterior = document.querySelector('#anterior');
    paginaAnterior.addEventListener('click', function() {
        if(paso <= pasoInicial) return;
        paso--;
        botonesPaginador();

    })
}

// Configura el evento para el botón de página siguiente
function paginaSiguiente() {
    const paginaSiguiente = document.querySelector('#siguiente');
    paginaSiguiente.addEventListener('click', function() {
        if(paso >= pasoFinal) return;
        paso++;
        botonesPaginador();
    })
}