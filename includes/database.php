<?php

// Establece la conexión a la base de datos MySQL
$db = mysqli_connect('localhost', 'root', 'Qltgfq86', 'apppeluqueria');

// Verifica si la conexión fue exitosa
if (!$db) {
     // Si la conexión falla, muestra un mensaje de error
    echo "Error: No se pudo conectar a MySQL.";
    echo "errno de depuración: " . mysqli_connect_errno();
    echo "error de depuración: " . mysqli_connect_error();
    exit;
}
