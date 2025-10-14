<?php

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'modelo/conexion_bd.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

// Leer datos JSON recibidos
$data = json_decode(file_get_contents("php://input"), true);
error_log(print_r($data, true));

// Validar JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Error al decodificar los datos JSON']);
    exit;
}

// Validar campos obligatorios
if (empty($data['email']) || empty($data['newPassword'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

//validar email
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$newPassword = $data['newPassword'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo electrónico inválido']);
    exit;
}

$stmt = $conexion->prepare("SELECT id_adm, nombre_adm FROM administrador WHERE correo_adm = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'El correo no está registrado']);
    exit;
}

$row = $result->fetch_assoc();
$nombre_adm = $row['nombre_adm'];


$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

$update = $conexion->prepare("UPDATE administrador SET pass_adm = ? WHERE correo_adm = ?");
$update->bind_param("ss", $hashedPassword, $email);

if (!$update->execute()) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar contraseña']);
    exit;
}

$plantilla_res = file_get_contents('restablecimiento.php'); // El archivo que tiene el HTML de plantilla
$plantilla_res = str_replace('{{LOGO_URL}}', 'https://ixah.giintapeinnovahue.com/images/LOGOTIPO_IXAH-02.png', $plantilla_res);
$plantilla_res= str_replace('{{LOGO_PIE_URL}}', 'https://giintapeinnovahue.com/images/logoGintapeCircle.png', $plantilla_res);
$plantilla_res = str_replace('{{NOMBRE_ADMIN}}', htmlspecialchars($nombre_adm), $plantilla_res );
$plantilla_res = str_replace('{{NUEVA_CONTRASENA}}', htmlspecialchars($newPassword), $plantilla_res );


$mail = new PHPMailer(true);
try {
    // Configuración SMTP con los datos del segundo bloque
    $mail->CharSet = "UTF-8";
    $mail->isSMTP();
    $mail->SMTPDebug = 0; 
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'STARTTLS'; 
    $mail->Host = "smtp-mail.outlook.com";
    $mail->Port = 587;

    // Credenciales
    $mail->Username = "contacto@giintapeinnovahue.com";
    $mail->Password = "$"; 

    // Configuración del correo
    $mail->setFrom("contacto@giintapeinnovahue.com", "Soporte");
    $mail->addAddress($email); // Aquí se envía al correo recibido en JSON

    $mail->isHTML(true);
    $mail->Subject = "Recuperación de Contraseña";
    $mail->Body = $plantilla_res;

    // Enviar correo
    $mail->send();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('Error al enviar correo: ' . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => 'Error al enviar correo: ' . $mail->ErrorInfo]);
}
