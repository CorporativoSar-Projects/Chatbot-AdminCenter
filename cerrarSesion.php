<!-- Funcion para cerrar la sesión del usuario -->

<?php
session_start();
session_destroy();
header("Location: index.php");
exit;
?>