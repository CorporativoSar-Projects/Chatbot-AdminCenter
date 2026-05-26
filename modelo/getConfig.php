<?php
session_start();
include 'conexion_bd.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_adm'])) {
    echo json_encode(["error" => "No hay administrador logueado"]);
    exit;
}

$id_adm = $_SESSION['id_adm'];

// 1. Obtener la empresa del administrador
$sqlEmp = "SELECT Empresa_id_emp FROM administrador WHERE id_adm = ? LIMIT 1";
$stmtEmp = $conexion->prepare($sqlEmp);
$stmtEmp->bind_param("i", $id_adm);
$stmtEmp->execute();
$resEmp = $stmtEmp->get_result();

if ($resEmp->num_rows === 0) {
    echo json_encode(["error" => "No se encontró la empresa del administrador"]);
    exit;
}

$empresa = $resEmp->fetch_assoc();
$id_emp = $empresa['Empresa_id_emp'];

// 2. Obtener configuración del chatbot
$sqlChat = "SELECT * FROM chatbot WHERE Administrador_id_adm = ? LIMIT 1";
$stmtChat = $conexion->prepare($sqlChat);
$stmtChat->bind_param("i", $id_adm);
$stmtChat->execute();
$resChat = $stmtChat->get_result();

if ($resChat->num_rows === 0) {
    echo json_encode(["error" => "No se encontró configuración de chatbot"]);
    exit;
}

$config = $resChat->fetch_assoc();

// 3. Verificar si la integración SFTP está activa
$sqlSftp = "SELECT tipo_integracion, activo, url_estandar  FROM integracion_sftp WHERE Empresa_id_emp = ? LIMIT 1";
$stmtSftp = $conexion->prepare($sqlSftp);
$stmtSftp->bind_param("s", $id_emp);
$stmtSftp->execute();
$resSftp = $stmtSftp->get_result();

// Valores por defecto
$config['integracion_activa'] = false;
$config['tipo_integracion'] = "estandar";
$config['url_estandar'] = null;

if ($resSftp->num_rows > 0) {
    $integ = $resSftp->fetch_assoc();

    $config['integracion_activa'] = (bool)$integ['activo'];
    $config['tipo_integracion'] = $integ['tipo_integracion'] ?? "estandar";
    $config['url_estandar'] = $integ['url_estandar'] ?? null; 
}


echo json_encode($config);

