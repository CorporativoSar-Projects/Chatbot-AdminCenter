<?php
// guardar_info_empresa.php
session_start();

if (!isset($_SESSION['id_adm'])) {
    header("Location: index.php");
    exit();
}

include 'modelo/conexion_bd.php';

$id_usuario = $_SESSION['id_adm'];
$mensaje = '';

try {
    // Recibir datos del formulario
    $nombre_empresa = $_POST['nombre_empresa'] ?? '';
    $resena = $_POST['resena'] ?? '';
    $correo_oficial = $_POST['correo_oficial'] ?? '';
    $direccion = $_POST['direccion'] ?? '';
    $facebook_url = $_POST['facebook_url'] ?? '';
    $instagram_url = $_POST['instagram_url'] ?? '';
    $linkedin_url = $_POST['linkedin_url'] ?? '';
    $mision = $_POST['mision'] ?? '';
    $vision = $_POST['vision'] ?? '';
    $valores = $_POST['valores'] ?? '';

    // Validar campos requeridos
    if (empty($nombre_empresa) || empty($correo_oficial)) {
        throw new Exception("Los campos marcados con * son obligatorios");
    }

    // Verificar si ya existe registro
    $sql_check = "SELECT id_empresa FROM informacion_empresa WHERE id_usuario = ?";
    $stmt_check = $conexion->prepare($sql_check);
    $stmt_check->bind_param("i", $id_usuario);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Actualizar registro existente
        $sql = "UPDATE informacion_empresa SET 
                nombre_empresa = ?, 
                resena = ?, 
                correo_oficial = ?, 
                direccion = ?, 
                facebook_url = ?, 
                instagram_url = ?, 
                linkedin_url = ?, 
                mision = ?, 
                vision = ?, 
                valores = ?, 
                fecha_actualizacion = NOW() 
                WHERE id_usuario = ?";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param(
            "ssssssssssi",
            $nombre_empresa,
            $resena,
            $correo_oficial,
            $direccion,
            $facebook_url,
            $instagram_url,
            $linkedin_url,
            $mision,
            $vision,
            $valores,
            $id_usuario
        );
        
        $accion = 'actualizada';
    } else {
        // Insertar nuevo registro
        $sql = "INSERT INTO informacion_empresa (
                id_usuario, 
                nombre_empresa, 
                resena, 
                correo_oficial, 
                direccion, 
                facebook_url, 
                instagram_url, 
                linkedin_url, 
                mision, 
                vision, 
                valores
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param(
            "issssssssss",
            $id_usuario,
            $nombre_empresa,
            $resena,
            $correo_oficial,
            $direccion,
            $facebook_url,
            $instagram_url,
            $linkedin_url,
            $mision,
            $vision,
            $valores
        );
        
        $accion = 'guardada';
    }

    if ($stmt->execute()) {
        $_SESSION['mensaje_exito'] = "Información de la empresa $accion correctamente";
    } else {
        throw new Exception("Error al guardar la información: " . $stmt->error);
    }

    // Redirigir al menú
    header("Location: menu.php?exito=1");
    exit();

} catch (Exception $e) {
    $_SESSION['mensaje_error'] = $e->getMessage();
    header("Location: informacion_empresa.php?error=1");
    exit();
}
?>