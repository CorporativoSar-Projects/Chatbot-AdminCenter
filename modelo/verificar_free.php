<?php
// verificar_free.php
include 'conexion_bd.php';

// Obtener la fecha de hoy
$hoy = date("Y-m-d");

// Buscar todos los planes Free activos cuyo fecha_fin ya pasó
$stmt = $conexion->prepare("
    SELECT h.id_historial, h.Empresa_id_emp, e.nombre_emp, h.fecha_fin
    FROM historial h
    INNER JOIN empresa e ON e.id_emp = h.Empresa_id_emp
    WHERE h.nombre_susc = 'Free' AND h.estado = 'activo' AND h.fecha_fin < ?
");
$stmt->bind_param("s", $hoy);
$stmt->execute();
$result = $stmt->get_result();

$planesCaducados = [];
while ($row = $result->fetch_assoc()) {
    $planesCaducados[] = $row;
}
$stmt->close();

// Actualizar los planes Free caducados
foreach ($planesCaducados as $plan) {
    $estado = "inactivo";
    $observaciones = "Plan Free caducado automáticamente";

    $stmt = $conexion->prepare("
        UPDATE historial 
        SET estado = ?, observaciones = ?
        WHERE id_historial = ?
    ");
    $stmt->bind_param("ssi", $estado, $observaciones, $plan['id_historial']);
    $stmt->execute();
    $stmt->close();

    // Opcional: registrar en log
    error_log("Plan Free caducado: Empresa {$plan['nombre_emp']} (ID: {$plan['Empresa_id_emp']}) fecha_fin: {$plan['fecha_fin']}");
}

// Mensaje final
echo count($planesCaducados) . " planes Free caducados procesados.\n";
?>
