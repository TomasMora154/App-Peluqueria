<?php

namespace Controllers;

use Model\Servicio;

class APIController {
    public static function index() {
        //header('Content-Type: application/json);
       $servicios = Servicio::all();
       echo json_encode($servicios);
    }
}