
<?php

session_start();
include 'conexion_bd.php';

//Datos del usuario que se ingresan al formulario de login

$id_adm = $_POST['id_adm'];
$correo_adm = $_POST['correo_adm'];
$pass_adm = $_POST['pass_adm'];

// Consulta segura con sentencias preparadas y prevenir la inyección SQL en la validación del usuario
// Insertar datos de manera segura con sentencia preparada y evitar la inyección SQL

/* IMPORTANTE!: este es un ejemplo de inicio de sesión con código maliciosos */
/* idEmpresa: ' OR 1=1 --
correo: ' OR '1'='1' -- 
contraseña: anything */
/* Si el sistema permite el inicio de sesión entonces estaría vulnerable */
$stmt = mysqli_prepare($conexion, "SELECT * FROM administrador WHERE id_adm = ? AND correo_adm = ?");
mysqli_stmt_bind_param($stmt, "ss", $id_adm, $correo_adm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // Se verifica la contraseña que sea igual a la encriptada
    // Si los datos son correctos se inicia la sesión
    if (password_verify($pass_adm, $row['pass_adm'])) {
        $_SESSION['id_adm'] = $id_adm;
        $_SESSION['correo_adm'] = $row['correo_adm']; 
        header("location: ../menu.php");
        exit;
    } else {
        header("location: ../index.php?error=1");
        exit;
    }
} else {
    header("location: ../index.php?error=1");

    exit;
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);
