<?php
ob_start();
session_start();
header('Content-Type: application/json; charset=utf-8');
include 'conexion_bd.php';

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// --- Zona horaria México ---
date_default_timezone_set('America/Mexico_City');


$inputJSON = file_get_contents('php://input');
$data = json_decode($inputJSON, true);

if (!$data || !isset($data['correo_adm'], $data['nombre_emp'], $data['nombre_susc'])) {
    echo json_encode(["status" => "error", "message" => "Datos de registro incompletos."]);
    exit;
}

// --- Validar reCAPTCHA ---
if (!isset($data['g-recaptcha-response']) || empty($data['g-recaptcha-response'])) {
    echo json_encode(["status" => "error", "message" => "Por favor completa el reCAPTCHA."]);
    exit;
}

$captcha = $data['g-recaptcha-response'];
$secretKey = ""; // Reemplaza con tu secret key real

$response = file_get_contents(
    "https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$captcha}"
);
$responseKeys = json_decode($response, true);

if (intval($responseKeys["success"]) !== 1) {
    echo json_encode(["status" => "error", "message" => "Error en reCAPTCHA, inténtalo de nuevo."]);
    exit;
}

// --- Función para generar ID de empresa ---
function generarIdEmp($nombre) {
    $base = substr(preg_replace('/[^a-zA-Z0-9]/', '', strtolower($nombre)), 0, 5);
    $sufijo = substr(time(), -4) . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 2);
    return $base . $sufijo;
}

try {
    // --- Validar campos básicos ---
    if (!$data || !isset($data['correo_adm'], $data['nombre_emp'], $data['nombre_susc'])) {
        echo json_encode(["status" => "error", "message" => "Datos de registro incompletos."]);
        exit;
    }

    // --- Generar ID de empresa si no viene ---
    if (empty($data['id_emp'])) {
        $data['id_emp'] = generarIdEmp($data['nombre_emp']);
    }

    // --- Verificar si la empresa ya existe ---
    $stmt = $conexion->prepare("SELECT id_emp FROM empresa WHERE RFC_emp = ?");
    $stmt->bind_param("s", $data['RFC_emp']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        echo json_encode(["status" => "error", "message" => "La empresa ya está registrada."]);
        exit;
    }
    $stmt->close();

    // --- Verificar si el correo del administrador ya existe ---
    $stmt = $conexion->prepare("SELECT correo_adm FROM administrador WHERE correo_adm = ?");
    $stmt->bind_param("s", $data['correo_adm']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        echo json_encode(["status" => "error", "message" => "El correo del administrador ya está registrado."]);
        exit;
    }
    $stmt->close();

    // --- Insertar empresa ---
    $stmt = $conexion->prepare("
        INSERT INTO empresa (id_emp, RFC_emp, nombre_emp, sitioweb_emp, codigoPostal_emp, estado_emp, url_cs_emp)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssssss",
        $data['id_emp'],
        $data['RFC_emp'],
        $data['nombre_emp'],
        $data['sitioweb_emp'],
        $data['codigoPostal_emp'],
        $data['estado_emp'],
        $data['url_cs_emp']
    );
    $stmt->execute();
    $stmt->close();

    // --- Insertar administrador ---
    $pass_hash = password_hash($data['pass_adm'], PASSWORD_DEFAULT);
    $stmt = $conexion->prepare("
        INSERT INTO administrador (correo_adm, pass_adm, nombre_adm, apellidop_adm, apellidom_adm, tel_adm, Empresa_id_emp)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssssss",
        $data['correo_adm'],
        $pass_hash,
        $data['nombre_adm'],
        $data['apellidop_adm'],
        $data['apellidom_adm'],
        $data['tel_adm'],
        $data['id_emp']
    );
    $stmt->execute();
    $stmt->close();

    // --- Obtener datos del plan Free ---
    $plan_nombre = $data['nombre_susc'];
    $stmt = $conexion->prepare("SELECT * FROM suscripcion WHERE nombre_susc = ?");
    $stmt->bind_param("s", $plan_nombre);
    $stmt->execute();
    $result = $stmt->get_result();
    $plan = $result->fetch_assoc();
    $stmt->close();

    if (!$plan) {
        echo json_encode(["status" => "error", "message" => "El plan seleccionado no existe."]);
        exit;
    }

    // --- Fechas del plan Free ---
    $fecha_contratacion = date("Y-m-d H:i:s");
    $fecha_fin = date("Y-m-d H:i:s", strtotime("+7 days"));
    $estado = "activo";

    // --- Insertar historial ---
    $stmt = $conexion->prepare("
        INSERT INTO historial (
            Empresa_id_emp, Suscripcion_id_susc, nombre_susc, 
            fecha_contratacion, fecha_fin, precio_susc, estado
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sisssss",
        $data['id_emp'],
        $plan['id_susc'],
        $plan['nombre_susc'],
        $fecha_contratacion,
        $fecha_fin,
        $plan['precio_susc'],
        $estado
    );
    $stmt->execute();
    $stmt->close();

    // --- Guardar sesión ---
    $_SESSION['id_emp'] = $data['id_emp'];
    $_SESSION['nombre_emp'] = $data['nombre_emp'];
    $_SESSION['correo_adm'] = $data['correo_adm'];

    // --- Enviar correo de confirmación ---
    try {
        $mail = new PHPMailer(true);
        $mail->CharSet = "UTF-8";
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
        $mail->Host = "smtp-mail.outlook.com";
        $mail->Port = 587;
        $mail->Username = "contacto@giintapeinnovahue.com";
        $mail->Password = "$";

        $mail->setFrom("contacto@giintapeinnovahue.com", "Soporte");
        $mail->addAddress($data['correo_adm']);

        $mail->isHTML(true);
        $mail->Subject = "Registro exitoso";

        $plantilla = file_get_contents(__DIR__ . '/envioId.php');
        $plantilla = str_replace('{{LOGO_URL}}', 'https://ixah.giintapeinnovahue.com/images/LOGOTIPO_IXAH-02.png', $plantilla);
        $plantilla = str_replace('{{LOGO_PIE_URL}}', 'https://giintapeinnovahue.com/images/logoGintapeCircle.png', $plantilla);
        $plantilla = str_replace('{{NOMBRE_EMPRESA}}', $data['nombre_emp'], $plantilla);
        $plantilla = str_replace('{{NOMBRE_ADMIN}}', $data['nombre_adm'], $plantilla);
        $plantilla = str_replace('{{ID_EMPRESA}}', $data['id_emp'], $plantilla);
        $plantilla = str_replace('{{URL_LOGIN}}', 'http://localhost/Chatbot-AdminCenter/index.php', $plantilla);

        $mail->Body = $plantilla;
        $mail->send();
    } catch (Exception $e) {
        error_log("Error al enviar correo: " . $mail->ErrorInfo);
    }

    // --- Finalizar registro ---
    echo json_encode([
        "status" => "success",
        "message" => "Registro exitoso"
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Error en el registro: " . $e->getMessage()]);
    exit;
}
?>
