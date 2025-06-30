<?php
session_start();
include 'conexion_bd.php';

// Recibir datos del login
$id_emp = trim($_POST['id_emp']);
$correo_adm = trim($_POST['correo_adm']);
$pass_adm = $_POST['pass_adm'];

// Comprobación de campos vacíos
if (empty($id_emp) || empty($correo_adm) || empty($pass_adm)) {
    echo "<script>console.log('Error: campos vacíos');</script>";
    header("Location: ../index.php?error=campos");
    exit;
}


// Consulta preparada para prevenir inyección SQL
$stmt = mysqli_prepare($conexion, "SELECT * FROM administrador WHERE correo_adm = ? AND Empresa_id_emp = ?");
mysqli_stmt_bind_param($stmt, "ss", $correo_adm, $id_emp);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Verificar si encontró usuario
if ($row = mysqli_fetch_assoc($result)) {
    // Verificar contraseña
    if (password_verify($pass_adm, $row['pass_adm'])) {
        // Iniciar sesión
        $_SESSION['id_adm'] = $row['id_adm'];
        $_SESSION['correo_adm'] = $row['correo_adm']; 
        $_SESSION['nombre_adm'] = $row['nombre_adm'];
        $_SESSION['apellidop_adm'] = $row['apellidop_adm'];
        $_SESSION['id_emp'] = $row['Empresa_id_emp'];

        echo "<script>console.log('Login exitoso: usuario encontrado');</script>";
        header("Location: ../menu.php");
        exit;
    } else {
        echo "<script>console.log('Error: contraseña incorrecta');</script>";
        header("Location: ../index.php?error=contrasena");
        exit;
    }
} else {
    echo "<script>console.log('Error: usuario no encontrado');</script>";
    header("Location: ../index.php?error=credenciales");
    exit;
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);
