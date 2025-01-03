<?php 

// Conectarnos a la base de datos
use Model\ActiveRecord;
// Requiere el autoload de Composer para cargar automáticamente las clases necesarias
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__); 
$dotenv->safeLoad();

// Requiere los archivo que contiene funciones útiles y la conexión a la base de datos
require 'funciones.php';
require 'database.php';

// Establece la conexión a la base de datos utilizando la configuración previamente definida en $db
ActiveRecord::setDB($db);