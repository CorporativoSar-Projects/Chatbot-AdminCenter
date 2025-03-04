<?php

session_start();

include 'conexion_bd.php';

$idEmpresa = $_POST['idEmpresa'];
$correo = $_POST['correo'];
$contra = $_POST['contra'];

$validar_login = mysqli_query($conexion, "SELECT * FROM
usuarios WHERE idEmpresa='$idEmpresa' and correo='$correo'
and contra='$contra'" );

if(mysqli_num_rows($validar_login)> 0) {
    $_SESSION['idEmpresa'] = $correo;

   header("location: ../menu.php");
   exit;
}else{
    echo '<script>
    alert("Id, correo o contraseña incorrectos");
    window.location = "../index.php";
    </script>
    ';

exit;
}


?>