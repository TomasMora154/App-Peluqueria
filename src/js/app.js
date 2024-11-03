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

    nombreCliente(); // Añade el nombre del cliente al objeto de cita
    seleccionarFecha(); // Añade la fecha de la cita en el objeto 
    seleccionarHora(); // Añade la hora de la cita en el objeto 

    mostrarResumen(); // Muestra le resumen de la cita
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
        mostrarResumen();
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

function nombreCliente() {
    cita.nombre = document.querySelector('#nombre').value;
}

function seleccionarFecha() {
    const inputFecha = document.querySelector('#fecha');
    inputFecha.addEventListener('input', function(e) { 

        const dia = new Date(e.target.value).getUTCDay();

        // Verifica si seleccionó día domingo
        if( [0].includes(dia) ) {
            e.target.value = '';
            mostrarAlerta('Domingos cerrado', 'error', '.formulario');
        } else {
            cita.fecha = e.target.value;
        }       
    })
}

function seleccionarHora() {
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e) {
        const horaCita = e.target.value;
        const [hora, minuto] = horaCita.split(":").map(Number); // Convierte hora y minuto a números

        // Horario de hora válida: 8:30 a.m a 11:45 a.m && 14:00 pm a 18:00 p.m
        const esHoraMananaValida = (hora === 8 && minuto >= 30) || (hora > 8 && hora < 11) || (hora === 11 && minuto <= 45);
        const esHoraTardeValida = (hora === 14 && minuto >= 0) || (hora > 14 && hora < 18) || (hora === 18 && minuto === 0);
        
        // Si es una hora válida
        if (esHoraMananaValida || esHoraTardeValida) {
            cita.hora = e.target.value;
            console.log(cita)
        } else {
            e.target.value = '';
            mostrarAlerta('Hora No Válida. Horarios disponibles: \n' +
                  '• Mañana: 08:30 a.m. - 11:45 a.m.\n' +
                  '• Tarde: 02:00 p.m. - 06:00 p.m.', 'error', '.formulario');
        }
    });
}

/*
function seleccionarHora() {
    const inputHora = document.querySelector('#hora');
    inputHora.addEventListener('input', function(e) {
        const horaCita = e.target.value;
        const hora = horaCita.split(":")[0];
        const minuto = horaCita.split(":")[1];
        // Horario de hora válida: 8:30 a.m a 11:45 a.m && 14:00 pm a 18:p.m
        if (hora < 8 && minuto < 30 || hora >= 11 && minuto >= 45 || hora < 14 || hora >= 18) {
            console.log('Hora no valida')
        } else {
            console.log('Hora Válida')
        }
    })
} */

// Muestra alerta cuando la cita es día domingo
function mostrarAlerta(mensaje, tipo, elemento, desaparece = true) {

    // Prevenir la creación de más de una alerta
    const alertaPrevia = document.querySelector('.alerta');
    if(alertaPrevia) {
        alertaPrevia.remove();
    }

    // Crear la alerta
    const alerta = document.createElement('DIV');
    alerta.textContent = mensaje;
    alerta.classList.add('alerta');
    alerta.classList.add(tipo);
    
    const referencia = document.querySelector(elemento);
    referencia.appendChild(alerta);
    
    if(desaparece) {
        // Desaparece la alerta a los 6 segundos
        setTimeout(() => {
            alerta.remove();
        }, 6000);
    }
}

function mostrarResumen() {
    const resumen = document.querySelector('.contenido-resumen');

    // Limpiar el contenido de Resumen
    while(resumen.firstChild) {
        resumen.removeChild(resumen.firstChild)
    }

    // Validar si llenó todos los campos de cita
    if ( Object.values(cita).includes('') || cita.servicios.length === 0) {
       mostrarAlerta('Debe seleccionar un Servicio, Fecha u Hora', 'error', '.contenido-resumen', false);
       return;
    } 

    // Mostrar Datos del Resumen, formatear el div de resumen
    const { nombre, fecha, hora, servicios } = cita;
    
    const nombreCliente = document.createElement('P');
    nombreCliente.innerHTML = `<span>Nombre:</span> ${nombre}`;

    const fechaCita = document.createElement('P');
    fechaCita.innerHTML = `<span>Fecha:</span> ${fecha}`;

    const horaCita = document.createElement('P');
    horaCita.innerHTML = `<span>Hora:</span> ${hora}`;

    // Mostrar los Servicios Seleccionados
    servicios.forEach(servicio => {
        const { id, precio, nombre } = servicio;
        const contenedorServicio = document.createElement('DIV');
        contenedorServicio.classList.add('contenedor-servicio');

        const textoServicio = document.createElement('P');
        textoServicio.textContent = nombre;

        const precioServicio = document.createElement('P');
        precioServicio.innerHTML = `<span>Precio:</span> ₡${precio}`;

        contenedorServicio.appendChild(textoServicio);
        contenedorServicio.appendChild(precioServicio);

        resumen.appendChild(contenedorServicio);
    })

    resumen.appendChild(nombreCliente);
    resumen.appendChild(fechaCita);
    resumen.appendChild(horaCita);
}
