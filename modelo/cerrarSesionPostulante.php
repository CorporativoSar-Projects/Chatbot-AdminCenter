<?php
session_start();

// Verificar si hay sesión de candidato activa
if (isset($_SESSION['candidato'])) {
    unset($_SESSION['candidato']); // Eliminar solo esa variable
}

// Destruir toda la sesión (importante para que no quede activa en el servidor)
session_unset();
session_destroy();

// Evitar cache del navegador
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status' => 'ok',
    'mensaje' => 'Sesión del postulante cerrada por inactividad'
]);
?>
