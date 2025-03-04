<?php
/* Si la conexión esta activa o no */
$conexion = mysqli_connect("localhost", "root", "", "login_registro", 3306);

/* if (!$conexion) {
    die("Error al conectar a la base de datos: " . mysqli_connect_error());
} else {
    echo "Conexión exitosa a la base de datos";
} */

$conexion->set_charset("utf8");

/* Código para mostrar si la conexión sigue activa o no: */

/* if ($conexion->ping()) {
    echo "La conexión a la base de datos está activa.";
} else {
    echo "La conexión a la base de datos no está activa.";
} */
?>
