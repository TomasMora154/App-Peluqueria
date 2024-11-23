<?php

// Establece la conexión a la base de datos MySQL
$db = mysqli_connect(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'], 
    $_ENV['DB_NAME'],
);

$db->set_charset('utf8');

// Verifica si la conexión fue exitosa
if (!$db) {
     // Si la conexión falla, muestra un mensaje de error
    echo "Error: No se pudo conectar a MySQL.";
    echo "errno de depuración: " . mysqli_connect_errno();
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}
