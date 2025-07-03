<?php

require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';

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
    $mail->Username = "jmoralesa@giintapeinnovahueteam.onmicrosoft.com";
    $mail->Password = "$"; 

    // Configuración del correo
    $mail->setFrom("jmoralesa@giintapeinnovahueteam.onmicrosoft.com", "Soporte");
    $mail->addAddress($email); // Aquí se envía al correo recibido en JSON

    $mail->isHTML(true);
    $mail->Subject = "Recuperación de Contraseña";
    $mail->Body = "<p>Hola,</p>
                   <p>Tu nueva contraseña es: <strong>" . htmlspecialchars($newPassword) . "</strong></p>
                   <p>Te recomendamos cambiarla después de iniciar sesión.</p>";

    // Enviar correo
    $mail->send();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('Error al enviar correo: ' . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => 'Error al enviar correo: ' . $mail->ErrorInfo]);
}
