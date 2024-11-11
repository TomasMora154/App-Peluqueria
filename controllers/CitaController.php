<?php

namespace Controllers;

use MVC\Router;

class CitaController {
    // Método estático que maneja la vista de la página de citas
    public static function index(Router $router) {
        // Iniciar la sesión para acceder a los datos de sesión
        session_start();

        // Renderizar la vista 'cita/index' y pasarle el nombre del usuario desde la sesión
        $router->render('cita/index', [
            'nombre' => $_SESSION['nombre'],
            'id' => $_SESSION['id']
        ]);
    }
}