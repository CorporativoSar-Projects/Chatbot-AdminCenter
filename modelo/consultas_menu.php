<?php
// consultas_menu.php
session_start();

if (!isset($_SESSION['id_adm'])) {
    session_destroy();
    header("location: ./index.php?error=2");
    exit;
}

include 'conexion_bd.php';

$id_adm = $_SESSION['id_adm'];

// Plan y estado de la suscripción
$sqlPlan = "SELECT s.nombre_susc, h.estado, h.fecha_contratacion
            FROM historial h
            INNER JOIN suscripcion s ON h.Suscripcion_id_susc = s.id_susc
            INNER JOIN empresa e ON h.Empresa_id_emp = e.id_emp
            WHERE e.id_emp = (SELECT empresa_id_emp FROM administrador WHERE id_adm = ?)
              AND h.estado IN ('activo', 'pendiente_cancelacion')
            ORDER BY h.fecha_contratacion DESC
            LIMIT 1";

$stmtPlan = $conexion->prepare($sqlPlan);
$stmtPlan->bind_param("i", $id_adm);
$stmtPlan->execute();
$resultPlan = $stmtPlan->get_result();

if ($rowPlan = $resultPlan->fetch_assoc()) {
    $planUsuario = $rowPlan['nombre_susc'];
    $estadoSuscripcion = strtolower($rowPlan['estado']);
    if ($estadoSuscripcion === 'cancelado') $estadoSuscripcion = 'canceled';
    if ($estadoSuscripcion === 'pausado') $estadoSuscripcion = 'paused';
} else {
    $planUsuario = "Sin plan";
    $estadoSuscripcion = "inactivo";
}

// ID de empresa
$sqlEmp = "SELECT Empresa_id_emp FROM administrador WHERE id_adm = ?";
$stmtEmp = $conexion->prepare($sqlEmp);
$stmtEmp->bind_param("i", $id_adm);
$stmtEmp->execute();
$resultEmp = $stmtEmp->get_result();
$id_emp = $resultEmp->fetch_assoc()['Empresa_id_emp'] ?? null;

// SFTP
$sqlSFTP = $conexion->prepare("SELECT * FROM integracion_sftp WHERE Empresa_id_emp = ?");
$sqlSFTP->bind_param("s", $id_emp);
$sqlSFTP->execute();
$resultSFTP = $sqlSFTP->get_result();
$sftpData = $resultSFTP->fetch_assoc() ?: [];
$sftpActivo = (int)($sftpData['activo'] ?? 0);

// Chatbots del administrador
$sql = "SELECT c.id_chatbot, c.inp_nombre, t.nombre_tipo_chatbot
        FROM chatbot c
        INNER JOIN tipo_chatbot t ON c.Tipo_Chatbot_idTipo_Chatbot = t.idTipo_Chatbot 
        WHERE c.Administrador_id_adm = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_adm);
$stmt->execute();
$result = $stmt->get_result();

$chatbots = [];

while ($row = $result->fetch_assoc()) {
    $chatbots[] = $row;
}


// Contar cuántos chatbots tiene el admin

$sqlCount = "SELECT COUNT(*) AS total_chatbots 
             FROM chatbot 
             WHERE Administrador_id_adm = ?";
$stmtCount = $conexion->prepare($sqlCount);
$stmtCount->bind_param("i", $id_adm);
$stmtCount->execute();
$resultCount = $stmtCount->get_result();
$rowCount = $resultCount->fetch_assoc();

$totalChatbots = (int)$rowCount['total_chatbots'];
?>
