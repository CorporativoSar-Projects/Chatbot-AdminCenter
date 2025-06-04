<?php
//Inicia la sesión 
session_start();

// Lee el request (que se envio con el fetch)
// 'php://input' permite acceder a datos del cuerpo de la solicitud (POST)
// json_decode convierte el JSON en un array 

$input = json_decode(file_get_contents('php://input'), true);


// Verifica que existan las claves 'seccion' y 'datos'
if (!isset($input['seccion']) || !isset($input['datos'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

// Se asignan variables locales 
$seccion = $input['seccion'];
$datos = $input['datos'];

// Se guardan los datos en la sesión 'chatbot' y dentro de ella la sección que correspondera
// Esto permite almacenar cada parte del chatbot (estilo, burbuja, etc.) mientras el usuario navega

$_SESSION['chatbot'][$seccion] = $datos;

echo json_encode(['success' => true]);
?>
