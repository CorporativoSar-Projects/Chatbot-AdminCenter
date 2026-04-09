<?php
include 'conexion_bd.php';

function consumirTokens($id_emp, $tokens_input, $tokens_output) {
    global $conexion;

    // Restar tokens y que no queden negativos
    $stmt = $conexion->prepare("
        UPDATE empresa
        SET tokens_actuales = GREATEST(tokens_actuales - ? - ?, 0)
        WHERE id_emp = ?
    ");
    $stmt->bind_param("iis", $tokens_input, $tokens_output, $id_emp);
    $stmt->execute();
    $stmt->close();

    // Devolver tokens restantes
    $stmt = $conexion->prepare("SELECT tokens_actuales FROM empresa WHERE id_emp = ?");
    $stmt->bind_param("s", $id_emp);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return $row['tokens_actuales'] ?? 0;
}
?>