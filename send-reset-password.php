<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

header('Content-Type: application/json');


$data = json_decode(file_get_contents("php://input"), true);
error_log(print_r($data, true)); 


if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'Error al decodificar los datos JSON']);
    exit;
}


if (!isset($data['email']) || !isset($data['newPassword'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$newPassword = $data['newPassword'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo electrónico inválido']);
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'localhost'; // Cambiar el servidor SMTP por el local
    $mail->SMTPAuth = false; // No se requiere autenticación para MailHog
    $mail->Port = 1025; // Puerto SMTP de MailHog

    $mail->setFrom('melomario57@gmail.com', 'Soporte'); 
    $mail->addAddress($email);
    $mail->Subject = 'Recuperación de Contraseña';
    $mail->isHTML(true);
    $mail->Body = "<p>Hola,</p>
                   <p>Tu nueva contraseña es: <strong>$newPassword</strong></p>
                   <p>Te recomendamos cambiarla después de iniciar sesión.</p>";

    $mail->send();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log('Error al enviar el correo: ' . $mail->ErrorInfo); 
    echo json_encode(['success' => false, 'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo]);
}
?>
