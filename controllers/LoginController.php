<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {
    // Método para manejar el login de un usuario
    public static function login(Router $router) {
        $alertas = [];
        // Verifica si la solicitud es de tipo POST
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
             // Crea una nueva instancia de Usuario con los datos del formulario
            $auth = new Usuario($_POST);
             // Valida los campos de inicio de sesión
            $alertas = $auth->validarLogin();

             // Si no hay alertas (errores de validación)
            if(empty($alertas)) {
                // Comprobar que exista el usuario
                $usuario = Usuario::where('email', $auth->email);
                
                if($usuario) {
                    // Verificar el password
                    if($usuario->comprobarPasswordAndVerificado($auth->password)) {
                        // Autenticar el Usuario
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre . " " . $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redirige al usuario según su rol (admin o usuario normal)
                        if($usuario->admin === "1") {
                            $_SESSION['admin'] = $usuario->admin ?? null;
                            header('Location: /admin');
                        } else {
                            header('Location: /cita');
                        }
                    }
                } else {
                    Usuario::setAlerta('error', 'Usuario no encontrado');
                }
            }
        }
        // Obtiene las alertas generadas
        $alertas = Usuario::getAlertas();
        // Renderiza la vista de login con las alertas
        $router->render('auth/login', [
            'alertas' => $alertas
        ]);
    }
    // Método para manejar el cierre de sesión
    public static function logout() {
        session_start();

        $_SESSION = [];

        header('Location: /');
    }

    // Método para manejar la solicitud de restablecer la contraseña
    public static function olvide(Router $router) {

        $alertas = [];
        // Verifica si la solicitud es de tipo POST
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Crea una nueva instancia de Usuario con los datos del formulario
            $auth = new Usuario($_POST);
             // Valida el email ingresado
            $alertas = $auth->validarEmail();

            if(empty($alertas)) {
                 // Busca al usuario en la base de datos por su email
                $usuario = Usuario::where('email', $auth->email);
                
                // Si el usuario existe y está confirmado
                if($usuario && $usuario->confirmado === "1") {
                    
                    // Genera un token para restablecer la contraseña
                    $usuario->crearToken();
                    $usuario->guardar();

                    // Envía un email con las instrucciones para restablecer la contraseña
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarIntrucciones();

                    // Añade una alerta de éxito
                    Usuario::setAlerta('exito', 'Revisa tu Email');
                } else {
                    Usuario::setAlerta('error', 'El Usario no existe o no está confirmado');
                }
            }
        }
        // Obtiene las alertas generadas
        $alertas = Usuario::getAlertas();

        // Renderiza la vista de "Olvidé mi contraseña" con las alertas
        $router->render('auth/olvide-password', [
            'alertas' => $alertas
        ]);
    }

    // Método para recuperar la contraseña
    public static function recuperar(Router $router) {

        $alertas = [];
        $error = false;

        // Obtiene el token de la URL
        $token = s($_GET['token']);
        
        // Buscar usuario por su token
        $usuario = Usuario::where('token', $token);

        // Si no se encuentra un usuario con ese token, genera una alerta de error
        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token No Válido');
            $error = true;
        }

        // Verifica si la solicitud es de tipo POST
        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Crea una nueva instancia de Usuario con los datos del formulario (nueva contraseña)
            $password = new Usuario($_POST);
            // Valida la nueva contraseña ingresada
            $alertas = $password->validarPassword();

            if(empty($alertas)) {
                // Guarda la nueva contraseña, la hashea, y elimina el token
                $usuario->password = null;
                $usuario->password = $password->password;
                $usuario->hashPassword();
                $usuario->token = null;

                // Guarda los cambios en la base de datos
                $resultado = $usuario->guardar();
                if($resultado) {
                    // Redirige al usuario a la página principal
                    header('Location: /');
                };
            }
        }

        // Obtiene las alertas generadas
        $alertas = Usuario::getAlertas();
        // Renderiza la vista de recuperación de contraseña
        $router->render('auth/recuperar-password', [
            'alertas' => $alertas,
            'error' => $error
        ]);
    }

    // Método para registrar un nuevo usuario
    public static function crear(Router $router) {

        $usuario = new Usuario;

        //Alertas vacias
        $alertas = [];
        // Verifica si la solicitud es de tipo POST
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sincroniza los datos del formulario con la instancia del usuario
            $usuario->sincronizar($_POST);
            // Valida los datos ingresados
            $alertas = $usuario->validarNuevaCuenta();

            // Revisar que alerta esté vacío
            if(empty($alertas)) {
                // Verificar que el usuario no esté registrado
                $resultado = $usuario->existeUsuario();
                if($resultado->num_rows) {
                    $alertas = Usuario::getAlertas();
                } else { // No está registrado
                    // Hasehar el password
                    $usuario->hashPassword();  

                    // Generar un token único
                    $usuario->crearToken();

                    // Enviar el email
                    $email = new Email($usuario->nombre, $usuario->email, $usuario->token);
                    $email->enviarConfirmacion();

                    // Crear el usuario
                    $resultado = $usuario->guardar();
                    if ($resultado) {
                        header('Location: /mensaje');
                    }

                    //debuguear($usuario);               
                }
            }
        }

        // Renderiza la vista de crear cuenta con las alertas y los datos del usuario
        $router->render('auth/crear-cuenta', [
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    // Método para mostrar el mensaje de confirmación de registro
    public static function mensaje(Router $router) {
        // Renderiza la vista de mensaje
        $router->render('auth/mensaje');
    }

    // Método para confirmar la cuenta de un usuario
    public static function confirmar(Router $router) {
        $alertas = [];
        // Obtiene el token de la URL
        $token = s($_GET['token']);
        // Busca al usuario por el token
        $usuario = Usuario::where('token', $token);
        
        // Si el token no es válido, genera una alerta de error
        if(empty($usuario)) {
            // Mostrar mensaje de error
            Usuario::setAlerta('error', 'Token No Válido');
        } else {
            //Modificar a usuario confirmado
            $usuario->confirmado = "1";
            $usuario->token = null;
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        // Obtener alertas
        $alertas = Usuario::getAlertas();

        // Renderizar la vista
        $router->render('auth/confirmar-cuenta', [
            'alertas' => $alertas
        ]);
    }
}