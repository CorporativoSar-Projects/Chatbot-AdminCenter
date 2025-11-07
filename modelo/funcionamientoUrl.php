<?php
include 'conexion_bd.php';
session_start();

header('Content-Type: application/json');

$id_adm = $_SESSION['id_adm'] ?? null;
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if (!$id_adm) {
    echo json_encode(["success" => false, "message" => "No hay sesión activa"]);
    exit;
}

// ---------------------
// VERIFICAR EXISTENCIA
// ---------------------
if ($accion === 'verificar') {
    // Paso 1: obtener id_empresa vinculada
    $sql = "SELECT Empresa_id_emp FROM administrador WHERE id_adm = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_adm);
    $stmt->execute();
    $stmt->bind_result($Empresa_id_emp);
    $stmt->fetch();
    $stmt->close();

    if (!$Empresa_id_emp) {
        echo json_encode(["success" => true, "url" => "", "editable" => true]);
        exit;
    }

    // Paso 2: obtener URL existente
    $sql2 = "SELECT url_cs_emp FROM empresa WHERE id_emp = ?";
    $stmt2 = $conexion->prepare($sql2);
    $stmt2->bind_param("s", $Empresa_id_emp);
    $stmt2->execute();
    $stmt2->bind_result($url);
    $stmt2->fetch();
    $stmt2->close();

    // --- NUEVO: Aseguramos valor correcto ---
    $url = trim($url ?? "");

    echo json_encode([
        "success" => true,
        "url" => $url,                // nombre esperado por JS
        "editable" => empty($url)     // editable solo si está vacío
    ]);
    exit;
}

// ---------------------
// GUARDAR NUEVA URL
// ---------------------
if ($accion === 'guardar') {
    $url_nueva = trim($_POST['url'] ?? '');

    if (empty($url_nueva)) {
        echo json_encode(["success" => false, "message" => "La URL no puede estar vacía."]);
        exit;
    }

    // Paso 1: obtener empresa del administrador
    $sql = "SELECT Empresa_id_emp FROM administrador WHERE id_adm = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_adm);
    $stmt->execute();
    $stmt->bind_result($Empresa_id_emp);
    $stmt->fetch();
    $stmt->close();

    if (!$Empresa_id_emp) {
        echo json_encode(["success" => false, "message" => "No se encontró la empresa asociada."]);
        exit;
    }

    // Paso 2: verificar si ya existe una URL
    $sqlCheck = "SELECT url_cs_emp FROM empresa WHERE id_emp = ?";
    $stmtCheck = $conexion->prepare($sqlCheck);
    $stmtCheck->bind_param("s", $Empresa_id_emp);
    $stmtCheck->execute();
    $stmtCheck->bind_result($urlExistente);
    $stmtCheck->fetch();
    $stmtCheck->close();

    if (!empty(trim($urlExistente))) {
        echo json_encode(["success" => false, "message" => "Ya existe una URL registrada."]);
        exit;
    }

    // Paso 3: guardar la nueva URL
    $sql2 = "UPDATE empresa SET url_cs_emp = ? WHERE id_emp = ?";
    $stmt2 = $conexion->prepare($sql2);
    $stmt2->bind_param("ss", $url_nueva, $Empresa_id_emp);


     if ($stmt2->execute()) {

         // === Generar/Actualizar JSON existente ===
    ob_start();
    include "generar_json.php"; // aquí tu script ya existente que crea o actualiza el JSON
    $json_output = ob_get_clean();

    echo json_encode([
        "success" => true,
        "message" => "La URL se ha guardado correctamente",
        "json" => json_decode($json_output, true)
    ]);

    } else {
        echo json_encode(["success" => false, "message" => "Error al guardar la URL."]);
    }
    $stmt2->close();
    $conexion->close();
    exit;
}

echo json_encode(["success" => false, "message" => "Acción no válida."]);
exit;
?>
