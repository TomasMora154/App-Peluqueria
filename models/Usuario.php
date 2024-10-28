<?php

namespace Model;

class Usuario extends ActiveRecord {
    // Nombre de la tabla en la base de datos
    protected static $tabla = 'usuarios';
    // Columnas de la tabla
    protected static $columnasDB = ['id', 'nombre' , 'apellido', 'email', 'password',
    'telefono', 'admin', 'confirmado', 'token'];

    // Propiedades del usuario
    public $id;
    public $nombre;
    public $apellido;
    public $email;
    public $password;
    public $telefono;
    public $admin;
    public $confirmado;
    public $token;

     // Constructor de la clase
    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->telefono = $args['telefono'] ?? '';
        $this->admin = $args['admin'] ?? '0';
        $this->confirmado = $args['confirmado'] ?? '0';
        $this->token = $args['token'] ?? '';
    }

    // Mensajes de validación para la creación de una cuenta
    public function validarNuevaCuenta() {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es obligatorio';
        }
        if (!$this->apellido) {
            self::$alertas['error'][] = 'El Apellido es obligatorio';
        }
        if (!$this->telefono) {
            self::$alertas['error'][] = 'El Telefono es obligatorio';
        }
        if (!$this->email) {
            self::$alertas['error'][] = 'El Email es obligatorio';
        }
        if (!$this->password) {
            self::$alertas['error'][] = 'El Password es obligatorio';
        }
        if (strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    // Valida los campos para el inicio de sesión
    public function validarLogin() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El email es Obligatorio';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'El password es Obligatorio';
        }

        return self::$alertas;
    }

    // Función para validar el Email
    public function validarEmail() {
        if(!$this->email) {
            self::$alertas['error'][] = 'El email es Obligatorio';
        }
        return self::$alertas;
    }

    // Función para validar la Contraseña
    public function validarPassword() {
        if(!$this->password) {
            self::$alertas['error'][] = 'La Contraseña es obligatoria';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'La Contraseña debe tener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    // Revisa si el usuario ya existe
    public function existeUsuario() {
        $query = " SELECT * FROM " . self::$tabla . " WHERE email = '" . $this->email . "' LIMIT 1";
        $resultado = self::$db->query($query);

        if($resultado->num_rows) {
            self::$alertas['error'][] = 'El usuario ya está registrado';
        }

        return $resultado;
    }

    // Funcion para hashear el password
    public function hashPassword() {
        $this->password = password_hash( $this->password, PASSWORD_BCRYPT);
    }

    // Funcion para crear un token único
    public function crearToken() {
        $this->token = uniqid();
    }

    // Valida la contraseña y si el usuario está confirmado
    public function comprobarPasswordAndVerificado($password) {
        $resultado = password_verify($password, $this->password);
        
        if(!$resultado || !$this->confirmado) {
            self::$alertas['error'][] = 'Contraseña Incorrecta o tu cuenta no ha sido confirmada'; 
        } else {
           return true;
        }
    }
}