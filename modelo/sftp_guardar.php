<?php
session_start();
header('Content-Type: application/json');
include 'conexion_bd.php';

if (!isset($_SESSION['id_adm'])) {
    echo json_encode(['success' => false, 'msg' => 'Sesión inválida']);
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
$servidor = isset($data['servidor']) ? trim($data['servidor']) : null;
$puerto = isset($data['puerto']) ? trim($data['puerto']) : '22';
$usuario = isset($data['usuario']) ? trim($data['usuario']) : null;
$contrasena = isset($data['contrasena']) ? trim($data['contrasena']) : null;
$rutaDestino = isset($data['rutaDestino']) ? trim($data['rutaDestino']) : null;

// Verificar si ya existe un registro para la empresa
$stmtCheck = $conexion->prepare("SELECT id_sftp FROM integracion_sftp WHERE Empresa_id_emp = ?");
$stmtCheck->bind_param("i", $id_emp);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

$ok = false;

if ($row = $resultCheck->fetch_assoc()) {
    // UPDATE registro existente
    if ($activo === 0) {
        // Solo desactivar
        $stmtUpdate = $conexion->prepare("UPDATE integracion_sftp SET activo=0 WHERE Empresa_id_emp=?");
        $stmtUpdate->bind_param("i", $id_emp);
    } else {
        // Validar campos obligatorios
        if (empty($servidor) || empty($usuario) || empty($rutaDestino)) {
            echo json_encode(['success' => false, 'msg' => 'Faltan campos obligatorios: servidor, usuario o ruta destino']);
            exit;
        }

        $hashContrasena = $contrasena ? password_hash($contrasena, PASSWORD_DEFAULT) : null;

        if ($hashContrasena) {
            // Actualizar incluyendo contraseña
            $stmtUpdate = $conexion->prepare("
                UPDATE integracion_sftp 
                SET servidor=?, puerto=?, usuario=?, contrasena=?, rutaDestino=?, activo=? 
                WHERE Empresa_id_emp=?
            ");
            $stmtUpdate->bind_param("sssssis", $servidor, $puerto, $usuario, $hashContrasena, $rutaDestino, $activo, $id_emp);
        } else {
            // Actualizar sin cambiar la contraseña
            $stmtUpdate = $conexion->prepare("
                UPDATE integracion_sftp 
                SET servidor=?, puerto=?, usuario=?, rutaDestino=?, activo=? 
                WHERE Empresa_id_emp=?
            ");
            $stmtUpdate->bind_param("ssssii", $servidor, $puerto, $usuario, $rutaDestino, $activo, $id_emp);
        }
    }

    $ok = $stmtUpdate->execute();
} else {
    // INSERT nuevo registro
    if ($activo === 0) {
       
         $ok = true;
    }

    // Validar campos obligatorios para crear
    if (empty($servidor) || empty($usuario) || empty($contrasena) || empty($rutaDestino)) {
        echo json_encode(['success' => false, 'msg' => 'Faltan campos obligatorios para crear SFTP']);
        exit;
    }

    $hashContrasena = password_hash($contrasena, PASSWORD_DEFAULT);

    $stmtInsert = $conexion->prepare("
        INSERT INTO integracion_sftp (Empresa_id_emp, servidor, puerto, usuario, contrasena, rutaDestino, activo)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $activoInsert = 1; 
    $stmtInsert->bind_param("isssssi", $id_emp, $servidor, $puerto, $usuario, $hashContrasena, $rutaDestino, $activoInsert);

    $ok = $stmtInsert->execute();
}

if ($ok) {
    // Generar JSON completo
    ob_start();
    include "generar_json.php";
    $json_output = ob_get_clean();

    echo json_encode([
        'success' => true,
        'msg' => 'Integración SFTP guardada',
        'json' => json_decode($json_output, true)
    ]);
} else {
    echo json_encode(['success' => false, 'msg' => $conexion->error]);
}

$conexion->close();
?>
