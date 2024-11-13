<?php

// Función para depurar variables
function debuguear($variable) : string {
    echo "<pre>";
    // Muestra información sobre la variable usando var_dump
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Sanitizar el HTML (eliminar o escapar caracteres y valores potencialmente dañinos de los datos de entrada)
function s($html) : string {
    // Utiliza htmlspecialchars para convertir caracteres especiales en entidades HTML
    $s = htmlspecialchars($html);
    return $s;
}

function esUltimo(string $actual, string $proximo): bool {
    if ($actual !== $proximo) {
        return true;
    }
    return false;
}

// Función que revisa que el usuario esté autenticado
function isAuth() : void {
    if(!isset($_SESSION['login'])) {
        header('Location: /');
    }
}