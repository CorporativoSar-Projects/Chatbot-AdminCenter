<?php
session_start();
header('Content-Type: application/json');
include 'conexion_bd.php';

// Clave secreta para encriptar/desencriptar
define('SECRET_KEY', 'tu_clave_secreta_super_segura_32_bytes'); // 32 caracteres para AES-256
define('SECRET_IV', '1234567890123456'); // 16 bytes para IV

function encrypt($string)
{
    return openssl_encrypt($string, "AES-256-CBC", SECRET_KEY, 0, SECRET_IV);
}

function decrypt($string)
{
    return openssl_decrypt($string, "AES-256-CBC", SECRET_KEY, 0, SECRET_IV);
}

// Verificar sesión
if (!isset($_SESSION['id_adm'])) {
    echo json_encode(['success' => false, 'msg' => 'Sesión inválida']);
    exit;
}

$nombrePlan = $_SESSION['nombre_plan'] ?? ''; // Ajusta el nombre si tu sesión usa otro campo


// Validación de seguridad: bloquear SFTP en plan Free
if (strtolower(trim($nombrePlan)) === 'free' && $data['tipo_integracion'] === 'sftp') {
    echo json_encode([
        "success" => false,
        "msg" => "La integración SFTP no está disponible en el plan Free."
    ]);
    exit;
}

$id_adm = $_SESSION['id_adm'];

// Obtener id de la empresa asociada al administrador
$sqlEmp = "SELECT Empresa_id_emp FROM administrador WHERE id_adm = ?";
$stmtEmp = $conexion->prepare($sqlEmp);
$stmtEmp->bind_param("i", $id_adm);
$stmtEmp->execute();
$resultEmp = $stmtEmp->get_result();

if ($rowEmp = $resultEmp->fetch_assoc()) {
    $id_emp = $rowEmp['Empresa_id_emp'];
} else {
    echo json_encode(['success' => false, 'msg' => 'No se encontró empresa asociada']);
    exit;
}

// Obtener datos enviados por fetch (JSON)
$data = json_decode(file_get_contents('php://input'), true);

$activo = isset($data['activo']) ? (int)$data['activo'] : 0;
$tipo = isset($data['tipo_integracion']) ? trim($data['tipo_integracion']) : 'sftp';

$servidor = isset($data['servidor']) ? trim($data['servidor']) : null;
$usuario = isset($data['usuario']) ? trim($data['usuario']) : null;
$contrasena = isset($data['contrasena']) ? trim($data['contrasena']) : null;
$rutaDestino = isset($data['rutaDestino']) ? trim($data['rutaDestino']) : null;

// Puerto fijo
$puerto = 22;

// URL para integración estándar
$url_estandar = isset($data['url_estandar']) ? trim($data['url_estandar']) : null;

// --- Verificar si ya existe un registro para la empresa ---
$stmtCheck = $conexion->prepare("SELECT id_sftp FROM integracion_sftp WHERE Empresa_id_emp = ?");
$stmtCheck->bind_param("s", $id_emp);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($row = $resultCheck->fetch_assoc()) {
    // --- Ya existe registro: UPDATE ---
    if ($activo === 0) {
        // Solo desactivar
        $stmtUpdate = $conexion->prepare("UPDATE integracion_sftp SET activo=0 WHERE Empresa_id_emp=?");
        $stmtUpdate->bind_param("s", $id_emp);
    } else {
        if ($tipo === 'sftp') {
            // Validación SFTP
            if (empty($servidor) || empty($usuario) || empty($rutaDestino)) {
                echo json_encode(['success' => false, 'msg' => 'Faltan campos obligatorios para SFTP']);
                exit;
            }

            if (!empty($contrasena)) {
                $encryptedPass = encrypt($contrasena);
                $stmtUpdate = $conexion->prepare("
                    UPDATE integracion_sftp 
                    SET tipo_integracion='sftp', servidor=?, puerto=?, usuario=?, contrasena=?, rutaDestino=?, activo=? 
                    WHERE Empresa_id_emp=?
                ");
                $stmtUpdate->bind_param("sssssis", $servidor, $puerto, $usuario, $encryptedPass, $rutaDestino, $activo, $id_emp);
            } else {
                $stmtUpdate = $conexion->prepare("
                    UPDATE integracion_sftp 
                    SET tipo_integracion='sftp', servidor=?, puerto=?, usuario=?, rutaDestino=?, activo=? 
                    WHERE Empresa_id_emp=?
                ");
                $stmtUpdate->bind_param("ssssis", $servidor, $puerto, $usuario, $rutaDestino, $activo, $id_emp);
            }
        } elseif ($tipo === 'estandar') {
            // Validación Estándar
            if (empty($url_estandar)) {
                echo json_encode(['success' => false, 'msg' => 'Debe proporcionar la URL para la integración estándar']);
                exit;
            }

            // Solo actualiza los campos de la integración estándar, sin borrar los SFTP
            $stmtUpdate = $conexion->prepare("
                UPDATE integracion_sftp 
                SET tipo_integracion='estandar', url_estandar=?, activo=? 
                WHERE Empresa_id_emp=?
            ");
            $stmtUpdate->bind_param("sis", $url_estandar, $activo, $id_emp);
        } else {
            echo json_encode(['success' => false, 'msg' => 'Tipo de integración desconocido']);
            exit;
        }
    }

    $ok = $stmtUpdate->execute();
    if (!$ok) {
        echo json_encode(['success' => false, 'msg' => 'Error al actualizar integración: ' . $stmtUpdate->error]);
        exit;
    }
} else {
    // --- Registro nuevo: INSERT ---
    if ($activo === 0) {
        echo json_encode(['success' => false, 'msg' => 'No se puede crear un registro desactivado']);
        exit;
    }

    if ($tipo === 'sftp') {
        if (empty($servidor) || empty($usuario) || empty($contrasena) || empty($rutaDestino)) {
            echo json_encode(['success' => false, 'msg' => 'Faltan campos obligatorios para crear SFTP']);
            exit;
        }

        $encryptedPass = encrypt($contrasena);
        $stmtInsert = $conexion->prepare("
            INSERT INTO integracion_sftp (Empresa_id_emp, tipo_integracion, servidor, puerto, usuario, contrasena, rutaDestino, activo)
            VALUES (?, 'sftp', ?, ?, ?, ?, ?, ?)
        ");
        $stmtInsert->bind_param("ssssssi", $id_emp, $servidor, $puerto, $usuario, $encryptedPass, $rutaDestino, $activo);
    } else {
        if (empty($url_estandar)) {
            echo json_encode(['success' => false, 'msg' => 'Debe proporcionar la URL para crear la integración estándar']);
            exit;
        }

        $stmtInsert = $conexion->prepare("
            INSERT INTO integracion_sftp (Empresa_id_emp, tipo_integracion, url_estandar, activo)
            VALUES (?, 'estandar', ?, ?)
        ");
        $stmtInsert->bind_param("ssi", $id_emp, $url_estandar, $activo);
    }

    $ok = $stmtInsert->execute();
    if (!$ok) {
        echo json_encode(['success' => false, 'msg' => 'Error al crear integración: ' . $stmtInsert->error]);
        exit;
    }
}


// --- Generar JSON completo ---
ob_start();
include "generar_json.php";
$json_output = ob_get_clean();




echo json_encode([
    'success' => true,
    'msg' => 'Integración guardada correctamente',
    'json' => json_decode($json_output, true),
    'url_estandar' => $url_estandar ?? null,
    'tipo_integracion' => $tipo,
    'activo' => $activo
]);

$conexion->close();