<?php

include 'conexion_bd.php';

$idEmpresa = $_POST['idEmpresa'];
$correo = $_POST['correo'];
$contra = $_POST['contra'];

$query = "INSERT INTO usuarios(idEmpresa, correo, contra) 
VALUES ('$idEmpresa', '$correo', '$contra')";

$ejecutar = mysqli_query( $conexion, $query);
if($ejecutar){
    echo "<script>alert('Registro exitoso'); 
    window.location = '../index.php';
    </script>
    ";
    
}else{
    echo "<script>alert('Registro no exitoso'); 
    window.location = '../index.php';
    </script>
    ";
}

myqli_close($conexion);
?>