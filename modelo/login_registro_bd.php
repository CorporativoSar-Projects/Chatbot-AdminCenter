<?php
include 'conexion_bd.php';

// Recibir datos
$id_adm = trim($_POST['id_adm']);
$correo_adm = filter_var($_POST['correo_adm']);
$pass_adm = trim($_POST['pass_adm']);
$nombre_adm = trim($_POST['nombre_adm']);
$apellidop_adm = trim($_POST['apellidop_adm']);
$apellidom_adm = trim($_POST['apellidom_adm']);
$tel_adm = trim($_POST['tel_adm']);
$Empresa_RFC_emp = $_POST['Empresa_RFC_emp'];

// Validación básica
if (!$correo_adm) {
    die("<script>alert('Correo inválido'); window.location = '../registerForm.php';</script>");
}

// Encriptar contraseña
$pass_adm_hash = password_hash($pass_adm, PASSWORD_DEFAULT);

// Verificar si ya existe el correo
$stmt = mysqli_prepare($conexion, "SELECT * FROM administrador WHERE correo_adm = ?");
mysqli_stmt_bind_param($stmt, "s", $correo_adm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) > 0) {
    echo "<script>alert('Este correo ya está registrado'); window.location = '../registerForm.php';</script>";
    exit();
}
mysqli_stmt_close($stmt);

// Verificar si ya existe el ID
$stmt = mysqli_prepare($conexion, "SELECT * FROM administrador WHERE id_adm = ?");
mysqli_stmt_bind_param($stmt, "s", $id_adm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if (mysqli_num_rows($result) > 0) {
    echo "<script>alert('Este ID ya está registrado'); window.location = '../registerForm.php';</script>";
    exit();
}
mysqli_stmt_close($stmt);

// Insertar nuevo administrador
$stmt = mysqli_prepare($conexion, "INSERT INTO administrador (id_adm, correo_adm, pass_adm, nombre_adm, apellidop_adm, apellidom_adm, tel_adm, Empresa_RFC_emp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssssssss", $id_adm, $correo_adm, $pass_adm_hash, $nombre_adm, $apellidop_adm, $apellidom_adm, $tel_adm, $Empresa_RFC_emp);
$ejecutar = mysqli_stmt_execute($stmt);

if ($ejecutar) {
    echo "<script>alert('Registro exitoso'); window.location = '../index.php';</script>";
} else {
    echo "<script>alert('Error en el registro'); window.location = '../registerForm.php';</script>";
}

mysqli_stmt_close($stmt);
mysqli_close($conexion);
