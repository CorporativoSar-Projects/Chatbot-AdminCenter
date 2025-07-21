<?php
session_start();
include 'conexion_bd.php';

    require '../PHPMailer-master/src/Exception.php';
    require '../PHPMailer-master/src/PHPMailer.php';
    require '../PHPMailer-master/src/SMTP.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Generar ID personalizado para empresa
    $nombre_empresa = trim($_POST['nombre_emp']);
    $base = substr(preg_replace('/[^a-zA-Z0-9]/', '', strtolower($nombre_empresa)), 0, 5);
    $sufijo = substr(time(), -4) . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 2);
    $id_emp = $base . $sufijo;

    // 2. Recibir datos de empresa
    $rfc_emp = strtoupper(trim($_POST['RFC_emp']));
    $nombre_emp = trim($_POST['nombre_emp']);
    $sitioweb_emp = trim($_POST['sitioweb_emp']);
    $codigoPostal_emp = trim($_POST['codigoPostal_emp']);
    $estado_emp = trim($_POST['estado_emp']);
    $url_cs_emp = trim($_POST['url_cs_emp']);


    // 3. Recibir datos del administrador
    $correo_adm = filter_var($_POST['correo_adm'], FILTER_SANITIZE_EMAIL);
    $pass_adm = trim($_POST['pass_adm']);
    $nombre_adm = trim($_POST['nombre_adm']);
    $apellidop_adm = trim($_POST['apellidop_adm']);
    $apellidom_adm = trim($_POST['apellidom_adm']);
    $tel_adm = trim($_POST['tel_adm']);


// Validación 
if (!$correo_adm) {
    die("<script>alert('Correo inválido'); window.location = '../registerForm.php';</script>");
}

// Encriptar contraseña
$pass_adm_hash = password_hash($pass_adm, PASSWORD_DEFAULT);

// 1. Verifica si ya existe la empresa
$stmt = mysqli_prepare($conexion, "SELECT * FROM empresa WHERE RFC_emp = ?");
mysqli_stmt_bind_param($stmt, "s", $rfc_emp);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) > 0) {
    echo "<script>alert('Este RFC ya está registrado.'); window.location = '../registerForm.php';</script>";
    exit();
}
mysqli_stmt_close($stmt);

// 2. Insertar empresa
$stmt = mysqli_prepare($conexion, "INSERT INTO empresa (id_emp, RFC_emp, nombre_emp, sitioweb_emp, codigoPostal_emp, estado_emp, url_cs_emp) VALUES (?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sssssss",$id_emp, $rfc_emp, $nombre_emp, $sitioweb_emp, $codigoPostal_emp, $estado_emp, $url_cs_emp);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// 3. Verifica si el correo del administrador ya existe
$stmt = mysqli_prepare($conexion, "SELECT * FROM administrador WHERE correo_adm = ?");
mysqli_stmt_bind_param($stmt, "s", $correo_adm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) > 0) {
    echo "<script>alert('Este correo ya está registrado'); window.location = '../registerForm.php';</script>";
    exit();
}
mysqli_stmt_close($stmt);

// 4. Insertar administrador
$stmt = mysqli_prepare($conexion, "INSERT INTO administrador (correo_adm, pass_adm, nombre_adm, apellidop_adm, apellidom_adm, tel_adm, Empresa_id_emp) VALUES (?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sssssss", $correo_adm, $pass_adm_hash, $nombre_adm, $apellidop_adm, $apellidom_adm, $tel_adm, $id_emp);
$ejecutar = mysqli_stmt_execute($stmt);

// Obtener el ID autoincremental del administrador 
$id_adm = mysqli_insert_id($conexion);

// Guardar en sesión lo necesario
$_SESSION['id_adm'] = $id_adm;
$_SESSION['nombre_adm'] = $nombre_adm;
$_SESSION['apellidop_adm'] = $apellidop_adm;
$_SESSION['correo_adm'] = $correo_adm;
$_SESSION['id_emp'] = $id_emp;
$_SESSION['nombre_emp'] = $nombre_emp;

if ($ejecutar) {


try {
    $mail = new PHPMailer(true);
    $mail->CharSet = "UTF-8";
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = 'tls';
     $mail->Host = "smtp-mail.outlook.com";
    $mail->Port = 587;

    // Credenciales
    $mail->Username = "contacto@giintapeinnovahue.com";
    $mail->Password = "$"; 

    // Configuración del correo
    $mail->setFrom("contacto@giintapeinnovahue.com", "Soporte");
    $mail->addAddress($correo_adm);

    $mail->isHTML(true);
    $mail->Subject = "Registro exitoso - ID de tu empresa";

    
     $plantilla = file_get_contents('envioId.php');

    $plantilla = str_replace('{{LOGO_URL}}', 'https://i.postimg.cc/RhxH6X8C/LOGO-GI-05.png', $plantilla);
    $plantilla = str_replace('{{NOMBRE_EMPRESA}}', htmlspecialchars($nombre_emp), $plantilla);
    $plantilla = str_replace('{{NOMBRE_ADMIN}}', htmlspecialchars($nombre_adm), $plantilla);
    $plantilla = str_replace('{{ID_EMPRESA}}', htmlspecialchars($id_emp), $plantilla);
    $plantilla = str_replace('{{URL_LOGIN}}', 'http://localhost/Chatbot-AdminCenter/index.php', $plantilla);
    
    $mail->Body = $plantilla;
    $mail->send();
    } catch (Exception $e) {
    error_log("Error al enviar correo: " . $mail->ErrorInfo);

    }
    header("Location: ../index.php"); 
    exit;
} else {
    echo "<script>
        alert('Error al registrar administrador');
        window.location = '../registerForm.php';
    </script>";
}

        }
mysqli_stmt_close($stmt);
mysqli_close($conexion);
?>
