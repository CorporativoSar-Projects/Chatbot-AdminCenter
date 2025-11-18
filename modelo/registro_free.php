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

// --- Función para generar ID de empresa ---
function generarIdEmp($nombre) {
    $base = substr(preg_replace('/[^a-zA-Z0-9]/', '', strtolower($nombre)), 0, 5);
    $sufijo = substr(time(), -4) . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 2);
    return $base . $sufijo;
}

try {
    // --- Validar datos recibidos ---
    if (!isset($_POST['registro'])) {
        exit("No se recibieron datos del registro.");
    }

    $reg = json_decode($_POST['registro'], true);

    if (!$reg || !isset($reg['correo_adm'], $reg['nombre_emp'], $reg['nombre_susc'])) {
        exit("Datos de registro incompletos.");
    }

    // --- Generar ID de empresa si no viene ---
    if (empty($reg['id_emp'])) {
        $reg['id_emp'] = generarIdEmp($reg['nombre_emp']);
    }

     // --- Verificar si la empresa ya existe ---
    $stmt = $conexion->prepare("SELECT id_emp FROM empresa WHERE RFC_emp = ?");
    $stmt->bind_param("s", $reg['RFC_emp']);
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
    $stmt->bind_param("s", $reg['correo_adm']);
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
        $reg['id_emp'],
        $reg['RFC_emp'],
        $reg['nombre_emp'],
        $reg['sitioweb_emp'],
        $reg['codigoPostal_emp'],
        $reg['estado_emp'],
        $reg['url_cs_emp']
    );
    $stmt->execute();
    $stmt->close();

    // --- Insertar administrador ---
    $pass_hash = password_hash($reg['pass_adm'], PASSWORD_DEFAULT);
    $stmt = $conexion->prepare("
        INSERT INTO administrador (correo_adm, pass_adm, nombre_adm, apellidop_adm, apellidom_adm, tel_adm, Empresa_id_emp)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssssss",
        $reg['correo_adm'],
        $pass_hash,
        $reg['nombre_adm'],
        $reg['apellidop_adm'],
        $reg['apellidom_adm'],
        $reg['tel_adm'],
        $reg['id_emp']
    );
    $stmt->execute();
    $stmt->close();

    // --- Obtener datos del plan seleccionado ---
    $plan_nombre = $reg['nombre_susc']; // viene del formulario
    $stmt = $conexion->prepare("SELECT * FROM suscripcion WHERE nombre_susc = ?");
    $stmt->bind_param("s", $plan_nombre);
    $stmt->execute();
    $result = $stmt->get_result();
    $plan = $result->fetch_assoc();
    $stmt->close();

    if (!$plan) {
        exit("El plan seleccionado no existe.");
    }

    // --- Fechas del plan ---
    $fecha_contratacion = date("Y-m-d H:i:s");
    // Si es Free, duración 7 días; si tiene duración definida, se puede ajustar según $plan
    $fecha_fin = ($plan['nombre_susc'] === 'free') ? date("Y-m-d H:i:s", strtotime("+7 days")) : null;
    $estado = "activo";

    // --- Insertar historial ---
    $stripe_subscription_id = ($plan['nombre_susc'] === 'free') ? null : ''; // luego se actualizará si hay Stripe
    $stripe_customer_id = ($plan['nombre_susc'] === 'free') ? null : '';

    $stmt = $conexion->prepare("
        INSERT INTO historial (
            Empresa_id_emp, Suscripcion_id_susc, nombre_susc, 
            fecha_contratacion, fecha_fin, precio_susc, estado, 
            stripe_subscription_id, stripe_customer_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sisssssss",
        $reg['id_emp'],
        $plan['id_susc'],
        $plan['nombre_susc'],
        $fecha_contratacion,
        $fecha_fin,
        $plan['precio_susc'],
        $estado,
        $stripe_subscription_id,
        $stripe_customer_id
    );
    $stmt->execute();
    $stmt->close();

    // --- Guardar sesión ---
    $_SESSION['id_emp'] = $reg['id_emp'];
    $_SESSION['nombre_emp'] = $reg['nombre_emp'];
    $_SESSION['correo_adm'] = $reg['correo_adm'];

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
        $mail->addAddress($reg['correo_adm']);

        $mail->isHTML(true);
        $mail->Subject = "Registro exitoso";

        $plantilla = file_get_contents(__DIR__ . '/envioId.php');
        $plantilla = str_replace('{{LOGO_URL}}', 'https://ixah.giintapeinnovahue.com/images/LOGOTIPO_IXAH-02.png', $plantilla);
        $plantilla = str_replace('{{LOGO_PIE_URL}}', 'https://giintapeinnovahue.com/images/logoGintapeCircle.png', $plantilla);
        $plantilla = str_replace('{{NOMBRE_EMPRESA}}', $reg['nombre_emp'], $plantilla);
        $plantilla = str_replace('{{NOMBRE_ADMIN}}', $reg['nombre_adm'], $plantilla);
        $plantilla = str_replace('{{ID_EMPRESA}}', $reg['id_emp'], $plantilla);
        $plantilla = str_replace('{{URL_LOGIN}}', 'http://localhost/Chatbot-AdminCenter/index.php', $plantilla);

        $mail->Body = $plantilla;
        $mail->send();
    } catch (Exception $e) {
        error_log("Error al enviar correo: " . $mail->ErrorInfo);
    }

    // --- Finalizar registro ---
    unset($_SESSION['registro']);
    echo json_encode([
    "status" => "success",
    "message" => "Registro exitoso"
]);
exit;

} catch (Exception $e) {
    exit("Error en el registro: " . $e->getMessage());
}
?>

