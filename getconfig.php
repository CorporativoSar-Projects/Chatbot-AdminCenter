<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); // permite lectura desde cualquier dominio

// Obtener id_emp desde GET
$id_emp = $_GET['id_emp'] ?? null;

if(!$id_emp){
    echo json_encode(['error' => 'No se especificó id_emp']);
    exit;
}

// Ruta al JSON según empresa
$configPath = __DIR__ . "/usuarios/$id_emp/config.json";

if(file_exists($configPath)){
    echo file_get_contents($configPath);
} else {
    echo json_encode(['error' => "No se encontró la configuración para $id_emp"]);
}
