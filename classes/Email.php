<?php

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {
    // Propiedades de la clase para almacenar la información del destinatario y el token de verificación
    public $email;
    public $nombre;
    public $token;

     // Constructor de la clase que recibe y asigna los valores de nombre, email y token
    public function __construct($nombre, $email, $token) {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->token = $token;
    }

    // Método para enviar el correo de confirmación de cuenta
    public function enviarConfirmacion() {

        // Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        // Remitente del correo
        $mail->setFrom('cuentas@apppeluqueria.com');
        $mail->addAddress($this->email, $this->nombre);
        $mail->Subject = 'Confirma tu cuenta';

        // Set HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        // Contenido del correo en formato HTML
        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has creado tu cuenta en 
        Peluquería Quirós, debes confirmarla presionando el siguiente enlace</p>";
        $contenido .= "<p>Presiona aquí: <a href='" .  $_ENV['APP_URL'] . "/confirmar-cuenta?token="
        . $this->token . "'>Confirmar Cuenta</a> </p>";
        $contenido .= "<p>Si tu no solicitaste esta cuenta, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";
        $mail->Body = $contenido;

        // Enviar el mail
        $mail->send();
    }

    public function enviarIntrucciones() { 

        // Crear el objeto de email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        // Remitente del correo
        $mail->setFrom('cuentas@apppeluqueria.com');
        $mail->addAddress($this->email, $this->nombre);
        $mail->Subject = 'Reestablece tu contraseña';

        // Set HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . $this->nombre . "</strong> Has solicitado reestablecer 
        tu contraseña, sigue el siguiente enlace para hacerlo</p>";
        $contenido .= "<p>Presiona aquí: <a href='" .  $_ENV['APP_URL'] . "/recuperar?token="
        . $this->token . "'>Reestablecer Contraseña</a> </p>";
        $contenido .= "<p>Si tu no solicitaste esta cambio, puedes ignorar el mensaje</p>";
        $contenido .= "</html>";
        $mail->Body = $contenido;

        // Enviar el mail
        $mail->send();
    }

}