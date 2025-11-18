<?php
include 'conexion_bd.php';
require __DIR__ . '/../vendor/autoload.php';

use phpseclib3\Net\SFTP;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');


session_start();

// --- CONTROL DE EXPIRACIÓN DE SESIÓN (30 minutos) ---
if (isset($_SESSION['token_expira'])) {
    if (time() > $_SESSION['token_expira']) {
        // Sesión expirada → destruir
        session_unset();
        session_destroy();
        echo json_encode([
            "sesion_expirada" => true,
            "mensaje" => "Tu sesión ha expirado. Por favor, vuelve a ingresar tu código de verificación."
        ]);
        exit;
    } else {
        // Renovar sesión si aún es válida (mantener activo mientras interactúa)
        $_SESSION['token_expira'] = time() + 120; // renovar otros 30 minutos
    }
}

// --- RECIBIR DATOS ---
$inputJSON   = file_get_contents('php://input');
$input       = json_decode($inputJSON, true);

$correo      = trim($input['correo_candidate'] ?? $_POST['correo_candidate'] ?? '');
$nombre      = trim($_POST['nombre_candidate'] ?? '');
$apellidop   = trim($_POST['apellidop_candidate'] ?? '');
$apellidom   = trim($_POST['apellidom_candidate'] ?? '');
$telefono    = trim($_POST['tel_candidate'] ?? '');
$id_emp      = $_POST['id_emp'] ?? null;
$id_vacante  = $_POST['idRequisicion'] ?? null;
$linkVacante = trim($_POST['linkVacante'] ?? '');
$tokenIngresado = trim($input['token'] ?? $_POST['token'] ?? '');


// --- VALIDAR CORREO ---
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "Correo inválido"]);
    exit;
}

// --- RECIBIR TOKEN ---
$tokenIngresado = trim($input['token'] ?? $_POST['token'] ?? '');

// --- VERIFICAR SI CANDIDATO EXISTE ---
$id_candidate = null;
$cvAntiguo = null;
$cvAntiguoId = null; // Inicializamos siempre
$nuevoCandidato = false;



$stmt = $conexion->prepare("SELECT id_candidate, nombre_candidate, apellidop_candidate, apellidom_candidate, tel_candidate, CV_candidate, CV_id_onedrive, token_verificacion, token_expira FROM candidato WHERE correo_candidate = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$stmt->store_result();
$existe = $stmt->num_rows > 0;
$stmt->bind_result($id_candidate, $nombreExist, $apellidopExist, $apellidomExist, $telExist, $cvAntiguoDb, $cvAntiguoIdDb, $tokenBD, $expiraBD);
$stmt->fetch();
$stmt->close();

$correoVerificado = $_SESSION['token_validado'] ?? false;

if ($existe) {
    // Candidato existente
    $cvAntiguo = $cvAntiguoDb;
    $cvAntiguoId = $cvAntiguoIdDb;


    if (!$correoVerificado || $_SESSION['correo_candidate'] !== $correo) {
        $tokenExpirado = !$tokenBD || strtotime($expiraBD) < time();

        if ($tokenExpirado && empty($tokenIngresado)) {
            // Generar token y enviar correo
            $token = rand(100000, 999999);
            $expiraToken = date("Y-m-d H:i:s", time() + 120);
            $stmtToken = $conexion->prepare("UPDATE candidato SET token_verificacion=?, token_expira=? WHERE correo_candidate=?");
            $stmtToken->bind_param("sss", $token, $expiraToken, $correo);
            $stmtToken->execute();
            $stmtToken->close();

            try {
                $mail = new PHPMailer(true);
                $mail->CharSet = "UTF-8";
                $mail->isSMTP();
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls';
                $mail->Host = "smtp-mail.outlook.com";
                $mail->Port = 587;
                $mail->Username = "contacto@giintapeinnovahue.com";
                $mail->Password = "$";

                $mail->setFrom("contacto@giintapeinnovahue.com", "Soporte");
                $mail->addAddress($correo);

                $mail->isHTML(true);
                $mail->Subject = "Código de verificación";

                $plantilla = file_get_contents(__DIR__ . '/plantillaToken.php');
                $plantilla = str_replace('{{LOGO_URL}}', 'https://ixah.giintapeinnovahue.com/images/LOGOTIPO_IXAH-02.png', $plantilla);
                $plantilla = str_replace('{{LOGO_PIE_URL}}', 'https://giintapeinnovahue.com/images/logoGintapeCircle.png', $plantilla);
                $plantilla = str_replace('{{TOKEN}}', $token, $plantilla);
                $plantilla = str_replace('{{EXPIRA}}', $expiraToken, $plantilla);

                $mail->Body = $plantilla;
                $mail->send();

                echo json_encode([
                    "requiere_token" => true,
                    "mensaje" => "Se ha enviado un token de verificación a tu correo"
                ]);
                exit;

            } catch (Exception $e) {
                echo json_encode(["error" => "No se pudo enviar el token: " . $mail->ErrorInfo]);
                exit;
            }
        }

        // Validar token ingresado
        if (!empty($tokenIngresado)) {
            if ($tokenIngresado !== $tokenBD || strtotime($expiraBD) < time()) {
                echo json_encode(["error" => "Token inválido o expirado"]);
                exit;
            }
            $_SESSION['correo_candidate'] = $correo;
            $_SESSION['token_validado'] = true;
            $_SESSION['token_expira'] = time() + 120; // 30 min
        } else {
            echo json_encode([
                "requiere_token" => true,
                "mensaje" => "Ingresa tu código de verificación"
            ]);
            exit;
        }
    }
} else {
    // Candidato nuevo → registrar directamente
    $_SESSION['correo_candidate'] = $correo;
    $_SESSION['token_validado'] = true;
    $_SESSION['token_expira'] = time() + 300; // 30 min
    $nombreExist = $nombre;
    $apellidopExist = $apellidop;
    $apellidomExist = $apellidom;
}
// --- FUNCIONES ONEDRIVE ---
function borrarCVOneDrive($accessToken, $fileId)
{
    if (!$fileId) return false;
    $userPrincipalName = "holaixah@giintapeinnovahueteam.onmicrosoft.com";
    $urlDelete = "https://graph.microsoft.com/v1.0/users/$userPrincipalName/drive/items/$fileId";
    $ch = curl_init($urlDelete);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $accessToken"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $status == 204;
}


// --- FUNCION PARA SUBIR CV A ONEDRIVE ---
function subirACvOneDrive($tmpPath, $nombreArchivo, $cvAntiguoId = null)
{
    $client_id     = ""; //el ID de la aplicación (Application ID).
    $client_secret = ""; //Clave secreta que da one Drive
    $tenant_id     = ""; //el identificador del directorio
    $userPrincipalName = "holaixah@giintapeinnovahueteam.onmicrosoft.com";
    $folderPath = "/Postulaciones";

    $urlToken = "https://login.microsoftonline.com/$tenant_id/oauth2/v2.0/token";
    $dataToken = [
        "client_id" => $client_id,
        "scope" => "https://graph.microsoft.com/.default",
        "client_secret" => $client_secret,
        "grant_type" => "client_credentials"
    ];

    $chToken = curl_init();
    curl_setopt($chToken, CURLOPT_URL, $urlToken);
    curl_setopt($chToken, CURLOPT_POST, true);
    curl_setopt($chToken, CURLOPT_POSTFIELDS, http_build_query($dataToken));
    curl_setopt($chToken, CURLOPT_RETURNTRANSFER, true);
    $resultToken = curl_exec($chToken);
    curl_close($chToken);

    $jsonToken = json_decode($resultToken, true);
    $accessToken = $jsonToken["access_token"] ?? null;
    if (!$accessToken) return null;

    // Borrar CV antiguo si existe
    if ($cvAntiguoId) {
        borrarCVOneDrive($accessToken, $cvAntiguoId);
    }

    $urlUpload = "https://graph.microsoft.com/v1.0/users/$userPrincipalName/drive/root:$folderPath/$nombreArchivo:/content";
    $fp = fopen($tmpPath, "r");
    $ch = curl_init($urlUpload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $accessToken", "Content-Type: application/pdf"]);
    curl_setopt($ch, CURLOPT_PUT, true);
    curl_setopt($ch, CURLOPT_INFILE, $fp);
    curl_setopt($ch, CURLOPT_INFILESIZE, filesize($tmpPath));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    if ($status != 200 && $status != 201) return null;

    $data = json_decode($response, true);

    $urlLink = "https://graph.microsoft.com/v1.0/users/$userPrincipalName/drive/items/" . $data['id'] . "/createLink";
    $ch2 = curl_init($urlLink);
    curl_setopt($ch2, CURLOPT_HTTPHEADER, ["Authorization: Bearer $accessToken", "Content-Type: application/json"]);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, json_encode(["type" => "view", "scope" => "anonymous"]));
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    $linkResponse = curl_exec($ch2);
    curl_close($ch2);

    $linkData = json_decode($linkResponse, true);
    return [
        "webUrl" => $linkData['link']['webUrl'] ?? null,
        "id"     => $data['id']
    ];
}

// --- MANEJO DEL CV ---
$nuevoCV = $cvAntiguo ?: null;
$cvNuevoId = $cvAntiguoId;
if (isset($_FILES['CV_candidate']) && $_FILES['CV_candidate']['error'] === 0) {
    $cv = $_FILES['CV_candidate'];
    $ext = strtolower(pathinfo($cv['name'], PATHINFO_EXTENSION));
    if ($ext !== 'pdf') {
        echo json_encode(["error" => "Solo se permiten archivos PDF"]);
        exit;
    }

    $nombreArchivo = uniqid() . "_" . preg_replace("/[^a-zA-Z0-9_-]/", "_", pathinfo($cv['name'], PATHINFO_FILENAME)) . ".pdf";

    $linkOneDriveData = subirACvOneDrive($cv['tmp_name'], $nombreArchivo, $cvAntiguoId ?? null);
    if (!$linkOneDriveData) {
        echo json_encode(["error" => "No se pudo subir el CV a OneDrive"]);
        exit;
    }

    $nuevoCV = $linkOneDriveData['webUrl'];
    $cvNuevoId = $linkOneDriveData['id'];
}

// --- INSERTAR O ACTUALIZAR CANDIDATO ---
if ($existe) {
    $params = [];
    $types = "";
    $updates = [];

    if ($nombre !== "") {
        $updates[] = "nombre_candidate=?";
        $params[] = $nombre;
        $types .= "s";
    } else {
        $nombre = $nombreExist;
    }
    if ($apellidop !== "") {
        $updates[] = "apellidop_candidate=?";
        $params[] = $apellidop;
        $types .= "s";
    } else {
        $apellidop = $apellidopExist;
    }
    if ($apellidom !== "") {
        $updates[] = "apellidom_candidate=?";
        $params[] = $apellidom;
        $types .= "s";
    } else {
        $apellidom = $apellidomExist;
    }
    if ($telefono !== "") {
        $updates[] = "tel_candidate=?";
        $params[] = $telefono;
        $types .= "s";
    } else {
        $telefono = $telExist;
    }
    if ($nuevoCV !== $cvAntiguo) {
        $updates[] = "CV_candidate=?";
        $params[] = $nuevoCV;
        $types .= "s";
        $updates[] = "CV_id_onedrive=?";
        $params[] = $cvNuevoId;
        $types .= "s";
    }

    if (count($updates) > 0) {
        $sql = "UPDATE candidato SET " . implode(", ", $updates) . " WHERE id_candidate=?";
        $params[] = $id_candidate;
        $types .= "i";
        $stmtUpdate = $conexion->prepare($sql);
        $stmtUpdate->bind_param($types, ...$params);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    }
} else {
    if (!$nuevoCV) {
        echo json_encode(["error" => "Debes subir un CV para registrarte."]);
        exit;
    }
    $stmtInsert = $conexion->prepare("INSERT INTO candidato (correo_candidate,nombre_candidate,apellidop_candidate,apellidom_candidate,tel_candidate,CV_candidate,CV_id_onedrive) VALUES (?,?,?,?,?,?,?)");
    $stmtInsert->bind_param("sssssss", $correo, $nombre, $apellidop, $apellidom, $telefono, $nuevoCV, $cvNuevoId);
    $stmtInsert->execute();
    $id_candidate = $stmtInsert->insert_id;
    $stmtInsert->close();
}


// --- INSERTAR POSTULACIÓN ---
if ($id_emp && $id_vacante) {
    $stmtCheck = $conexion->prepare("SELECT id_postulacion 
    FROM postulaciones 
    WHERE Candidato_id_candidate=? AND id_vacante=? AND Empresa_id_emp=?
");
    $stmtCheck->bind_param("iss", $id_candidate, $id_vacante, $id_emp);
    $stmtCheck->execute();
    $stmtCheck->store_result();
    $yaPostulado = $stmtCheck->num_rows > 0;
    $stmtCheck->close();

    if ($yaPostulado) {
        echo json_encode(["tipo" => "alerta", "mensaje" => "Ya te has postulado a esta vacante"]);
        exit;
    }

    $stmtPost = $conexion->prepare("INSERT INTO postulaciones (Candidato_id_candidate, Empresa_id_emp, id_vacante, link_vacante) VALUES (?,?,?,?)");
    $stmtPost->bind_param("isss", $id_candidate, $id_emp, $id_vacante, $linkVacante);
    $stmtPost->execute();
    $stmtPost->close();

    // --- OBTENER CONFIGURACIÓN DEL CSV ---
    $jsonPath = __DIR__ . "/../usuarios/$id_emp/config.json";
    if (!file_exists($jsonPath)) {
        echo json_encode(["error" => "No se encontró el archivo JSON para la empresa $id_emp"]);
        exit;
    }
    $configData = json_decode(file_get_contents($jsonPath), true);

    $urlVacantesCSV = null;
    if (!empty($configData['conversacion']) && is_array($configData['conversacion'])) {
        foreach ($configData['conversacion'] as $conv) {
            if (!empty($conv['urlInforme'])) {
                $urlVacantesCSV = $conv['urlInforme'];
                break;
            }
        }
    }
    if (!$urlVacantesCSV) {
        echo json_encode(["error" => "No se encontró la URL del CSV de vacantes en el JSON"]);
        exit;
    }

    // --- LEER CSV DE VACANTES ---
    $vacantesMap = [];
    $ch = curl_init($urlVacantesCSV);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $csvContent = curl_exec($ch);
    $curlErr = curl_error($ch);
    curl_close($ch);
    if ($csvContent === false) {
        echo json_encode(["error" => "No se pudo leer el CSV: $curlErr"]);
        exit;
    }

    $tmp = fopen('php://memory', 'r+');
    fwrite($tmp, $csvContent);
    rewind($tmp);
    fgetcsv($tmp); // encabezado
    while (($data = fgetcsv($tmp)) !== false) {
        $vacantesMap[trim($data[0])] = trim($data[2]);
    }
    fclose($tmp);

    // --- OBTENER RUTA DESTINO PARA CSV ---
    $stmtRuta = $conexion->prepare("SELECT rutaDestino, servidor, puerto, usuario, contrasena FROM integracion_sftp WHERE Empresa_id_emp=? AND activo=1");
    $stmtRuta->bind_param("s", $id_emp);
    $stmtRuta->execute();
    $resRuta = $stmtRuta->get_result();
    $rowRuta = $resRuta->fetch_assoc();
    $stmtRuta->close();

    $rutaDestino = $rowRuta['rutaDestino'] ?? null;

    if (!$rutaDestino) {
        echo json_encode(["error" => "No se ha configurado la ruta de destino para esta empresa"]);
        exit;
    }

    $rutaDestino = str_replace("\\", "/", $rutaDestino);


    if (!is_dir($rutaDestino)) {
        mkdir($rutaDestino, 0777, true);
    }
    $csvFile = rtrim($rutaDestino, '/') . "/postulaciones_{$id_emp}.csv";


    // --- CREAR CSV ---
    $fp = fopen($csvFile, 'w');
    fputcsv($fp, ["Nombre", "ApellidoP", "ApellidoM", "Correo", "Teléfono", "CV_Link", "Vacante", "Link_Vacante"]);

    $stmtCSV = $conexion->prepare("
        SELECT c.nombre_candidate, c.apellidop_candidate, c.apellidom_candidate,
               c.correo_candidate, c.tel_candidate, c.CV_candidate,
               p.id_vacante, p.link_vacante
        FROM postulaciones p
        INNER JOIN candidato c ON p.Candidato_id_candidate=c.id_candidate
        WHERE p.Empresa_id_emp=?
    ");
    $stmtCSV->bind_param("s", $id_emp);
    $stmtCSV->execute();
    $resultCSV = $stmtCSV->get_result();
    while ($row = $resultCSV->fetch_assoc()) {
        fputcsv($fp, [
            $row['nombre_candidate'],
            $row['apellidop_candidate'],
            $row['apellidom_candidate'],
            $row['correo_candidate'],
            $row['tel_candidate'],
            $row['CV_candidate'],
            $vacantesMap[$row['id_vacante']] ?? $row['id_vacante'],
            $row['link_vacante']
        ]);
    }
    fclose($fp);
    $stmtCSV->close();

    // --- SUBIR CSV A SFTP ---
    define('SECRET_KEY', 'tu_clave_secreta_super_segura_32_bytes');
    define('SECRET_IV', '1234567890123456');
    function decryptSFTP($string)
    {
        return openssl_decrypt($string, "AES-256-CBC", SECRET_KEY, 0, SECRET_IV);
    }

    if (!empty($rowRuta['servidor'])) {
        $sftp = new SFTP($rowRuta['servidor'], (int)$rowRuta['puerto']);
        $pass = decryptSFTP($rowRuta['contrasena']);
        if ($sftp->login($rowRuta['usuario'], $pass)) {
            $remotePath = rtrim($rowRuta['rutaDestino'], '/') . "/postulaciones.csv";
            if (!$sftp->put($remotePath, $csvFile, SFTP::SOURCE_LOCAL_FILE)) {
                error_log("Error subiendo CSV SFTP empresa $id_emp");
            }
        } else {
            error_log("No se pudo autenticar SFTP empresa $id_emp");
        }
    }

    $cvLinkTexto = $nuevoCV ? '<a href="' . $nuevoCV . '" target="_blank">Ver CV</a>' : 'No disponible';

    // --- CSV interno global ---
    $csvInterno = __DIR__ . "/../interno/postulaciones_internas.csv";
    $fp2 = fopen($csvInterno, 'w');
    fputcsv($fp2, ["Nombre", "ApellidoP", "ApellidoM", "Correo", "Teléfono", "CV_Link", "Vacante", "Link_Vacante", "Empresa"]);
    $stmtInterno = $conexion->prepare("SELECT c.nombre_candidate, c.apellidop_candidate, c.apellidom_candidate, c.correo_candidate, c.tel_candidate, c.CV_candidate, p.id_vacante, p.link_vacante, p.Empresa_id_emp FROM postulaciones p INNER JOIN candidato c ON p.Candidato_id_candidate=c.id_candidate");
    $stmtInterno->execute();
    $resInterno = $stmtInterno->get_result();
    while ($row = $resInterno->fetch_assoc()) {
        $nombreVacante = $vacantesMap[$row['id_vacante']] ?? $row['id_vacante'];
        fputcsv($fp2, [
            $row['nombre_candidate'],
            $row['apellidop_candidate'],
            $row['apellidom_candidate'],
            $row['correo_candidate'],
            $row['tel_candidate'],
            $row['CV_candidate'],
            $nombreVacante,
            $row['link_vacante'],
            $row['Empresa_id_emp']
        ]);
    }
    fclose($fp2);
    $stmtInterno->close();

    $cvLinkFinal = $nuevoCV ?: $cvAntiguoDb ?: null;

    echo json_encode([

        "mensaje" => "Postulación enviada correctamente",
        "nombre_candidate" => $nombre,
        "apellidop_candidate" => $apellidop,
        "apellidom_candidate" => $apellidom,
        "tel_candidate" => $telefono,
        "cv_link" => $cvLinkTexto
    ]);
} else {
    echo json_encode([
        "valido" => true,
        "mensaje" => "Datos de candidato obtenidos",
        "nombre_candidate" => $nombre,
        "apellidop_candidate" => $apellidop,
        "apellidom_candidate" => $apellidom,
        "tel_candidate" => $telefono,
        "cv_link" => $nuevoCV
    ]);
}
