<?php
$conexion = mysqli_connect("localhost", "root", "", "login", 3306);

if (!$conexion) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
} else {
    echo "Conexión exitosa a la base de datos";
}

$conexion->set_charset("utf8");
?>