<?php 

// Se incluye el archivo de configuración y la inicialización de la aplicación
require_once __DIR__ . '/../includes/app.php';

//use Controllers\LoginController;

// Se importan los controladores necesarios para manejar las solicitudes

use Controllers\APIController;
use Controllers\CitaController;
use Controllers\LoginController;
use MVC\Router;

// Se crea una nueva instancia del enrutador
$router = new Router();

// Iniciar Sesión
$router->get('/', [LoginController::class, 'login']); // Maneja la solicitud GET para mostrar el formulario de inicio de sesión
$router->post('/', [LoginController::class, 'login']); // Maneja la solicitud POST para procesar el inicio de sesión
// Cerrar sesión
$router->get('/logout', [LoginController::class, 'logout']); // Maneja la solicitud para cerrar sesión

// Recuperar password
$router->get('/olvide', [LoginController::class, 'olvide']); // Muestra el formulario para recuperar la contraseña
$router->post('/olvide', [LoginController::class, 'olvide']); // Procesa la solicitud de recuperación de contraseña
$router->get('/recuperar', [LoginController::class, 'recuperar']); // Muestra el formulario para recuperar la cuenta
$router->post('/recuperar', [LoginController::class, 'recuperar']); // Procesa la recuperación de cuenta

// Rutas para crear una nueva cuenta
$router->get('/crear-cuenta', [LoginController::class, 'crear']); // Muestra el formulario para crear una nueva cuenta
$router->post('/crear-cuenta', [LoginController::class, 'crear']); // Procesa la creación de una nueva cuenta

// Ruta para confirmar la cuenta creada
$router->get('/confirmar-cuenta', [LoginController::class, 'confirmar']); // Maneja la solicitud para confirmar la cuenta
$router->get('/mensaje', [LoginController::class, 'mensaje']); // Muestra un mensaje después de la confirmación

// Area Privada
$router->get('/cita', [CitaController::class, 'index']);

// API de Citas
$router->get('/api/servicios', [APIController::class, 'index']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();