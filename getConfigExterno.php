<?php
// getConfigExterno.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$id_emp = basename($_GET['archivo'] ?? '');
if(!$id_emp) { echo json_encode(["error"=>"No se especificó archivo"]); exit; }

$path = __DIR__ . "/usuarios/$id_emp/config.json";
if(!file_exists($path)) { echo json_encode(["error"=>"JSON no encontrado"]); exit; }

echo file_get_contents($path);
