<?php
session_start();
require 'conexion_bd.php'; 
// Verificar que el administrador haya iniciado sesión
if (!isset($_SESSION['id_adm'])) {
    echo json_encode(['success' => false, 'error' => 'No hay sesión de administrador']);
    exit;
}

$id_adm = $_SESSION['id_adm'];
$id_tipo_chatbot = 1; // ID fijo para el tipo de chatbot

// Verificar que todas las secciones estén completas en la sesión agrupada
//Cuando el usuario presiona el botón 'guardar' para guardar el chatbot completo, 
//se recuperan todas las secciones desde $_SESSION['chatbot']

if (
    !isset($_SESSION['chatbot']['estilo']) || 
    !isset($_SESSION['chatbot']['burbuja']) || 
    !isset($_SESSION['chatbot']['mensaje_inicial']) || 
    !isset($_SESSION['chatbot']['conversacion']) || 
    !isset($_SESSION['chatbot']['despedida'])
) {
    echo json_encode(['success' => false, 'error' => 'Faltan datos en la sesión del chatbot']);
    exit;
}

// Extraer cada sección
$estilo     = $_SESSION['chatbot']['estilo'];
$burbuja    = $_SESSION['chatbot']['burbuja'];
$inicial    = $_SESSION['chatbot']['mensaje_inicial'];
$conversa   = $_SESSION['chatbot']['conversacion'];
$despedida  = $_SESSION['chatbot']['despedida'];

//Consulta SQL
$sql = "INSERT INTO chatbot (
    `inp_nombre`, `colorPrimario`, `colorSecundario`, `colorTexto`,
    `colorAcento`, `colorRespuestaUsuario`, `urlLogotipo`, `inp_burbuja`,
    `inp_saludo`, `inp_conversa1`, `inp_conversa2`, `inp_conversa3`,
    `inp_mensaje_usuario`, `inp_columna`, `inp_url_informe`,
    `inp_mensaje_usuario2`, `inp_columna2`,
    `inp_mensaje_usuario3`, `inp_columna3`, `inp_url_informe3`,
    `inp_despedida`, `Tipo_Chatbot_idTipo_Chatbot`, `Administrador_id_adm`
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "ssssssssssssssssssssssi",
    $estilo['inp_nombre'],
    $estilo['colorPrimario'],
    $estilo['colorSecundario'],
    $estilo['colorTexto'],
    $estilo['colorAcento'],
    $estilo['colorUsuario'],
    $estilo['urlLogotipo'],
    $burbuja['inp_burbuja'],
    $inicial['inp_saludo'],
    $inicial['inp_conversa1'],
    $inicial['inp_conversa2'],
    $inicial['inp_conversa3'],
    $conversa['inp_mensaje_usuario'],
    $conversa['inp_columna'],
    $conversa['inp_url_informe'],
    $conversa['inp_mensaje_usuario2'],
    $conversa['inp_columna2'],
    $conversa['inp_mensaje_usuario3'],
    $conversa['inp_columna3'],
    $conversa['inp_url_informe3'],
    $despedida['inp_despedida'],
    $id_tipo_chatbot,
    $id_adm
);

// Ejecuta la consulta
if ($stmt->execute()) {
    unset($_SESSION['chatbot']);
    echo json_encode(['success' => true, 'message' => 'Chatbot insertado correctamente']);
} else {
    echo json_encode(['success' => false, 'error' => 'Error al insertar: ' . $stmt->error]);
}
error_log("ID del administrador en sesión: " . $id_adm);

$stmt->close();
$conexion->close();
?>
