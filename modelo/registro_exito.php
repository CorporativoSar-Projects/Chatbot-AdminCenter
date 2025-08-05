<?php
// Mostrar todos los errores y activar excepciones de mysqli para mejor debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();
session_start();
require __DIR__ . '/../vendor/autoload.php';
include 'conexion_bd.php';

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

\Stripe\Stripe::setApiKey('sk_test_51...');//private key de stripe

$session_id = $_GET['session_id'] ?? null;
if (!$session_id) {
    exit("No se recibió sesión de pago.");
}

try {
    // Recuperar sesión de Stripe
    $session = \Stripe\Checkout\Session::retrieve($session_id);

   
    error_log("Sesión Stripe recuperada: " . print_r($session, true));

    if ($session->payment_status !== 'paid') {
        exit("Pago no completado.");
    }

    // Obtener token únicop ara vincular registro temporal
    $token = $session->client_reference_id;
    if (!$token) {
        exit("No se recibió client_reference_id en la sesión de pago.");
    }

    // Recuperar datos temporales del registro desde la DB
    $stmt = $conexion->prepare("SELECT datos FROM registro_tmp WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        exit("No se encontraron datos del registro para este pago.");
    }

    $fila = $resultado->fetch_assoc();
    $reg = json_decode($fila['datos'], true);
    $stmt->close();

    // DEBUG: log datos decodificados
    error_log("Datos de registro recuperados: " . print_r($reg, true));

    // Validar datos necesarios
    if (!$reg || !isset($reg['id_emp'], $reg['correo_adm'], $reg['nombre_emp'])) {
        exit("Datos de registro incompletos.");
    }

    // Verificar si la empresa ya está registrada
    $stmt = $conexion->prepare("SELECT id_emp FROM empresa WHERE RFC_emp = ?");
    $stmt->bind_param("s", $reg['RFC_emp']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        exit("Empresa ya registrada.");
    }
    $stmt->close();

    // Insertar empresa
    $stmt = $conexion->prepare("INSERT INTO empresa (id_emp, RFC_emp, nombre_emp, sitioweb_emp, codigoPostal_emp, estado_emp, url_cs_emp) VALUES (?, ?, ?, ?, ?, ?, ?)");
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
    if (!$stmt->execute()) {
        error_log("Error al insertar empresa: " . $stmt->error);
        exit("Error al insertar empresa: " . $stmt->error);
    }
    $stmt->close();
    error_log("Empresa insertada correctamente.");

    // Insertar administrador 
    $pass_hash = password_hash($reg['pass_adm'], PASSWORD_DEFAULT);;

    $stmt = $conexion->prepare("INSERT INTO administrador (correo_adm, pass_adm, nombre_adm, apellidop_adm, apellidom_adm, tel_adm, Empresa_id_emp) VALUES (?, ?, ?, ?, ?, ?, ?)");
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
    if (!$stmt->execute()) {
        error_log("Error al insertar administrador: " . $stmt->error);
        exit("Error al insertar administrador: " . $stmt->error);
    }
    $stmt->close();
    error_log("Administrador insertado correctamente.");

    // Insertar historial de suscripción
    $tipo_susc = $reg['nombre_susc'];
    $stmt = $conexion->prepare("SELECT id_susc, linkPago_susc, cicloPago_susc, precio_susc FROM suscripcion WHERE nombre_susc = ?");
    $stmt->bind_param("s", $tipo_susc);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if (!$row) {
        exit("Suscripción no válida.");
    }

    $id_susc = $row['id_susc'];
    $precio_susc = $row['precio_susc'];
    $fecha_contratacion = date("Y-m-d");
    $estado = "activo";

    $session_expanded = \Stripe\Checkout\Session::retrieve($session_id, ['expand' => ['subscription']]);
   $stripe_subscription_id = $session->subscription;


    $stmt = $conexion->prepare("INSERT INTO historial (Empresa_id_emp, Suscripcion_id_susc, fecha_contratacion, precio_susc, estado, stripe_subscription_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissss", $reg['id_emp'], $id_susc, $fecha_contratacion, $precio_susc, $estado, $stripe_subscription_id);
    if (!$stmt->execute()) {
        error_log("Error al insertar historial: " . $stmt->error);
        exit("Error al insertar historial: " . $stmt->error);
    }
    $stmt->close();
    error_log("Historial insertado correctamente.");

    // Opcional: eliminar datos temporales
    $stmt = $conexion->prepare("DELETE FROM registro_tmp WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->close();

    // Guardar datos en sesión para el usuario actual
    $_SESSION['id_emp'] = $reg['id_emp'];
    $_SESSION['nombre_emp'] = $reg['nombre_emp'];
    $_SESSION['correo_adm'] = $reg['correo_adm'];

    // Enviar correo de confirmación

    error_log("Intentando enviar correo a " . $reg['correo_adm']);
    try {
        $mail = new PHPMailer(true);
        $mail->CharSet = "UTF-8";
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';
       $mail->Host ="smtp-mail.outlook.com";
        $mail->Port = 587;
        $mail->Username = "contacto@giintapeinnovahue.com";
        $mail->Password = "$";

       $mail->setFrom("contacto@giintapeinnovahue.com", "Soporte");
        $mail->addAddress($reg['correo_adm']);

        $mail->isHTML(true);
        $mail->Subject = "Registro exitoso - ID de tu empresa";

        $plantilla = file_get_contents(__DIR__ . '/envioId.php');
        $plantilla = str_replace('{{LOGO_URL}}', 'https://i.postimg.cc/RhxH6X8C/LOGO-GI-05.png', $plantilla);
        $plantilla = str_replace('{{NOMBRE_EMPRESA}}', $reg['nombre_emp'], $plantilla);
        $plantilla = str_replace('{{NOMBRE_ADMIN}}', $reg['nombre_adm'], $plantilla);
        $plantilla = str_replace('{{ID_EMPRESA}}', $reg['id_emp'], $plantilla);
        $plantilla = str_replace('{{URL_LOGIN}}', 'http://localhost/Chatbot-AdminCenter/index.php', $plantilla);

        $mail->Body = $plantilla;
        $mail->send();
    } catch (Exception $e) {
        error_log("Error al enviar correo: " . $mail->ErrorInfo);
    }

    // Limpiar sesión temporal y redirigir
    unset($_SESSION['registro']);
    //ob_end_clean();
    header("Location: ../index.php");
    exit;

} catch (\Stripe\Exception\ApiErrorException $e) {
    exit("Error en Stripe: " . $e->getMessage());
} catch (Exception $e) {
    exit("Error general: " . $e->getMessage());
}
