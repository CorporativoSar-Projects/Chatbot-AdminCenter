<?php
include 'conexion_bd.php';
session_start();

$id_adm = $_SESSION['id_adm'] ?? null;
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if (!$id_adm) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "No hay sesión activa"]);
    exit;
}

if ($accion === 'verificar') {
    // Paso 1: Obtener id_empresa
    $sql = "SELECT Empresa_id_emp FROM administrador WHERE id_adm = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_adm);
    $stmt->execute();
    $stmt->bind_result($Empresa_id_emp);
    $stmt->fetch();
    $stmt->close();

    // Validar si se obtuvo id_empresa
    if (!$Empresa_id_emp) {
        header('Content-Type: application/json');
        echo json_encode(["url" => "", "editable" => true]);
        exit;
    }

    // Paso 2: Obtener URL desde empresa
    $sql2 = "SELECT url_cs_emp FROM empresa WHERE id_emp = ?";
    $stmt2 = $conexion->prepare($sql2);
    $stmt2->bind_param("s", $Empresa_id_emp);
    $stmt2->execute();
    $stmt2->bind_result($url);
    $stmt2->fetch();
    $stmt2->close();

   file_put_contents("debug.txt", "ID empresa usada: [$Empresa_id_emp]\nURL leída desde BD: [$url]");
    header('Content-Type: application/json');
    echo json_encode([
        "url" => $url,
        "editable" => empty(trim($url))
    ]);
    exit;
}

if ($accion === 'guardar') {
    $url_nueva = $_POST['url'] ?? '';

    if (empty($url_nueva)) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "La URL no puede estar vacía."]);
        exit;
    }

    // Obtener id_empresa
    $sql = "SELECT 	Empresa_id_emp FROM administrador WHERE id_adm = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_adm);
    $stmt->execute();
    $stmt->bind_result($Empresa_id_emp);
    $stmt->fetch();
    $stmt->close();

    if (!$Empresa_id_emp) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "No se encontró la empresa."]);
        exit;
    }

    // Actualizar URL
    $sql2 = "UPDATE empresa SET url_cs_emp = ? WHERE id_emp = ?";
    $stmt2 = $conexion->prepare($sql2);
    $stmt2->bind_param("ss", $url_nueva, $Empresa_id_emp);

    header('Content-Type: application/json');
    if ($stmt2->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al guardar la URL."]);
    }

    $stmt2->close();
    $conexion->close();
    exit;
}
