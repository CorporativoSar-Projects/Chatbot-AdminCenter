<?php

session_start();
include 'conexion_bd.php';

$idEmpresa = $_POST['idEmpresa'];
$correo = $_POST['correo'];
$contra = $_POST['contra'];

// Consulta segura con sentencias preparadas y prevenir la inyección SQL en la validación del usuario
// Insertar datos de manera segura con sentencia preparada y evitar la inyección SQL

/* IMPORTANTE!: este es un ejemplo de inicio de sesión con código maliciosos */
/* idEmpresa: ' OR 1=1 --
correo: ' OR '1'='1' -- 
contraseña: anything */
/* Si el sistema permite el inicio de sesión entonces estaría vulnerable */
$stmt = mysqli_prepare($conexion, "SELECT * FROM usuarios WHERE idEmpresa = ? AND correo = ?");
mysqli_stmt_bind_param($stmt, "ss", $idEmpresa, $correo);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // Se verifica la contraseña que sea igual a la encriptada
    if (password_verify($contra, $row['contra'])) {
        $_SESSION['idEmpresa'] = $correo;
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
