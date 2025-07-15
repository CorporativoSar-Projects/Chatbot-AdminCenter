<?php
session_start();
include 'conexion_bd.php'; // Ajusta ruta a tu conexión

if (!isset($_SESSION['id_adm'])) {
    session_destroy();
    header("location: ../index.php?error=2");
    exit;
}

// Verificar si el usuario solicitó un chatbot nuevo
if (isset($_GET['nuevo']) && $_GET['nuevo'] == 1) {
    $chatbot = []; // Nuevo chatbot
    unset($_SESSION['id_chatbot']); // Limpiar sesión anterior
} else {
    $id_chatbot = $_GET['id_chatbot'] ?? $_SESSION['id_chatbot'] ?? null;

    if ($id_chatbot) {
        $_SESSION['id_chatbot'] = $id_chatbot;

        //obtener los datos desde la BD
        $sql = "SELECT * FROM chatbot WHERE id_chatbot = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("i", $id_chatbot);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $chatbot = $result->fetch_assoc();
            } else {
                $chatbot = []; 
            }
        } else {
            $chatbot = [];
        }
    } else {
        $chatbot = []; 
    }
}
?>
