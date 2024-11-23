<?php

namespace MVC;
// Maneja rutas y genera vistas
class Router
{
    // Arrays para almacenar las rutas GET y POST
    public array $getRoutes = [];
    public array $postRoutes = [];

    // Método para registrar una ruta GET
    public function get($url, $fn)
    {
        // Asocia una URL a una función (controlador) que se ejecutará al acceder a esa ruta
        $this->getRoutes[$url] = $fn;
    }

    // Método para registrar una ruta POST
    public function post($url, $fn)
    {
        // Asocia una URL a una función (controlador) que se ejecutará al acceder a esa ruta con método POST
        $this->postRoutes[$url] = $fn;
    }

    // Método para comprobar la ruta solicitada y ejecutar el controlador correspondiente
    public function comprobarRutas()
    {
        
        // Proteger Rutas...
        session_start();

        // Arreglo de rutas protegidas...
        // $rutas_protegidas = ['/admin', '/propiedades/crear', '/propiedades/actualizar', '/propiedades/eliminar', '/vendedores/crear', '/vendedores/actualizar', '/vendedores/eliminar'];

        // $auth = $_SESSION['login'] ?? null;

         // Obtener la URL actual de la solicitud o usar '/' por defecto si no está disponible
        $currentUrl = strtok($_SERVER['REQUEST_URI'], '?') ?? '/';
        // Obtener el método de la solicitud (GET o POST)
        $method = $_SERVER['REQUEST_METHOD'];

        // Determinar la función (controlador) asociada a la URL según el método de solicitud
        if ($method === 'GET') {
            $fn = $this->getRoutes[$currentUrl] ?? null;
        } else {
            $fn = $this->postRoutes[$currentUrl] ?? null;
        }

         // Si existe una función asociada a la URL, se ejecuta
        if ( $fn ) {
            // Call user fn va a llamar una función cuando no sabemos cual sera
            call_user_func($fn, $this); // This es para pasar argumentos
        } else {
            echo "Página No Encontrada o Ruta no válida";
        }
    }

    // Método para renderizar una vista y pasarle datos
    public function render($view, $datos = [])
    {

        // Extrae los datos del array asociativo y los convierte en variables individuales
        foreach ($datos as $key => $value) {
            $$key = $value;  // Crea una variable dinámica con el nombre de la clave y su valor
        }

        ob_start(); // Almacenamiento en memoria durante un momento...

        // entonces incluimos la vista en el layout
        include_once __DIR__ . "/views/$view.php";
        $contenido = ob_get_clean(); // Limpia el Buffer
        include_once __DIR__ . '/views/layout.php';
    }
}
