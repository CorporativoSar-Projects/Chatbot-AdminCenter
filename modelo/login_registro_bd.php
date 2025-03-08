<?php

include 'conexion_bd.php';

$idEmpresa = trim($_POST['idEmpresa']);
$correo = filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL);
$contra = trim($_POST['contra']);

if (!$correo) {
    die("<script>alert('Correo inválido'); window.location = '../registerForm.php';</script>");
}


// Código para encriptar la contraseña
$contra = password_hash($contra, PASSWORD_DEFAULT);

// Verificar que el correo no se repita en la base de datos
$stmt = mysqli_prepare($conexion, "SELECT * FROM usuarios WHERE correo = ?");
mysqli_stmt_bind_param($stmt, "s", $correo);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo "<script>alert('Este correo ya está registrado, prueba con otro distinto'); 
    window.location = '../registerForm.php';
    </script>";
    exit();
}
mysqli_stmt_close($stmt);

// Verificar que el usuario no se repita en la base de datos
$stmt = mysqli_prepare($conexion, "SELECT * FROM usuarios WHERE idEmpresa = ?");
mysqli_stmt_bind_param($stmt, "s", $idEmpresa);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo "<script>alert('Este usuario ya está registrado'); 
    window.location = '../registerForm.php';
    </script>";
    exit();
}
mysqli_stmt_close($stmt);

// Insertar datos de manera segura con sentencia preparada y evitar la inyección SQL

/* IMPORTANTE!: este es un ejemplo de registro de usuario con código maliciosos */
/* idEmpresa: test123
correo: test@example.com' -- 
contraseña: password */
/* Si el sistema permite el registro entonces estaría vulnerable */
$stmt = mysqli_prepare($conexion, "INSERT INTO usuarios (idEmpresa, correo, contra) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sss", $idEmpresa, $correo, $contra);
$ejecutar = mysqli_stmt_execute($stmt);

if ($ejecutar) {
    echo "<script>alert('Registro exitoso'); 
    window.location = '../index.php';
    </script>";
} else {
    echo "<script>alert('Registro no exitoso'); 
    window.location = '../registerForm.php';
    </script>";
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);
