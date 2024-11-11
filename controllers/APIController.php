<?php

namespace Controllers;

use Model\Cita;
use Model\Servicio;

class APIController {
    public static function index() {
        //header('Content-Type: application/json);
       $servicios = Servicio::all();
       echo json_encode($servicios);
    }

    public static function guardar() {

        $cita = new Cita($_POST);
        $resultado = $cita->guardar();
        echo json_encode($resultado);
    }
}