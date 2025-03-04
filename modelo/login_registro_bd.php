<?php

include 'conexion_bd.php';

$idEmpresa = $_POST['idEmpresa'];
$correo = $_POST['correo'];
$contra = $_POST['contra'];

//Codígo para encriptar la contraseña
$contra = hash('sha512', $contra);

$query = "INSERT INTO usuarios(idEmpresa, correo, contra) 
VALUES ('$idEmpresa', '$correo', '$contra')";

/* Verificar que el correo no se repita en la base de datos */

$verificar_correo = mysqli_query($conexion,"SELECT * FROM usuarios WHERE correo='$correo' ");

if(mysqli_num_rows($verificar_correo) > 0) {
    echo "<script>alert('Este correo ya está registrado, prueba con otro distinto'); 
    window.location = '../registerForm.php';
    </script>
    ";
    exit();
}

/* Verificar que el usuario no se repita en la base de datos */

$verificar_idEmpresa = mysqli_query($conexion,"SELECT * FROM usuarios WHERE idEmpresa='$idEmpresa' ");

if(mysqli_num_rows($verificar_idEmpresa) > 0) {
    echo "<script>alert('Este usuario ya está registrado'); 
    window.location = '../registerForm.php';
    </script>
    ";
    exit();
}

$ejecutar = mysqli_query( $conexion, $query);
if($ejecutar){
    echo "<script>alert('Registro exitoso'); 
    window.location = '../index.php';
    </script>
    ";
    
}else{
    echo "<script>alert('Registro no exitoso'); 
    window.location = '../registerForm.php';
    </script>
    ";
}

myqli_close($conexion);
?>