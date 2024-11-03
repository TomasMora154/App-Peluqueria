// Inicializa el paso actual y define los límites de pasos
let paso = 1;
const pasoInicial = 1;
const pasoFinal = 3;

const cita = {
    nombre: '',
    fecha: '',
    hora: '',
    servicios: []
}

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

    consultarAPI(); // Consulta la API en el backend de PHP
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

async function consultarAPI() {
    try {
        const url = 'http://localhost:3000/api/servicios';
        const resultado = await fetch(url);
        const servicios = await resultado.json();
        mostrarServicios(servicios);

    } catch (error) {
        console.log(error);
    }
}

function mostrarServicios(servicios) {
    servicios.forEach( servicio => {
        const { id, nombre, precio } = servicio;

        const nombreServicio = document.createElement('P');
        nombreServicio.classList.add('nombre-servicio');
        nombreServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.classList.add('precio-servicio');
        precioServicio.textContent = `₡${precio}`;

        const servicioDiv = document.createElement('DIV');
        servicioDiv.classList.add('servicio');
        servicioDiv.dataset.idServicio = id;
        servicioDiv.onclick = function(){
            seleccionarServicio(servicio);
        }

        servicioDiv.appendChild(nombreServicio);
        servicioDiv.appendChild(precioServicio);

        document.querySelector('#servicios').appendChild(servicioDiv);
    })
}

function seleccionarServicio(servicio) {
    //Extraer el id del servicio
    const { id } = servicio;
    // Extraer el arreglo de servicios
    const { servicios } = cita;
    // Identificar el elemento al que se le da clic
    const divServicio = document.querySelector(`[data-id-servicio="${id}"]`);

    // Comprobar si un servicio ya fue agregado
    if( servicios.some( agregado => agregado.id === id ) ) {
        //  Si ya está agregado, eliminarlo
        cita.servicios = servicios.filter( agregado => agregado.id !== id )
        // Se le elimina el id de seleccionado
        divServicio.classList.remove('seleccionado')
    } else {
        // Si no existe, agregarlo
        // Tomo una copia de los servicios y agrego uno nuevo
        cita.servicios = [...servicios, servicio];
        // Se le agrega el id de seleccionado
        divServicio.classList.add('seleccionado')
    }
    console.log(cita);
}