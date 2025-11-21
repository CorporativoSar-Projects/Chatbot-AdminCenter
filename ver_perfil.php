<?php
// Validación de sesión
include 'modelo/consultas_menu.php';

// Obtener ID del perfil desde la URL
$id = $_GET['id'] ?? null;
if (!$id) {
    die("❌ No se especificó el ID del perfil.");
}

// Consultar al backend Django
$api_url = "http://localhost:8000/perfiles/" . $id;

$perfilJson = file_get_contents($api_url);
$perfil = json_decode($perfilJson, true);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/sty.css" />
    <title>Perfil de Puesto</title>
    <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
</head>

<body>

    <!-- HEADER (copiado de tu layout) -->
    <div class="rectangulo-container">
        <img src="img/Logo_cabeza.svg" width="70px" alt="Logo" class="img-logo-chiq">
    </div>

    <header>
        <!--<nav class="navbar">
            <ul class="filas">
                <li><a href="menu.php" class="txt-home">Home</a></li>
                <li><a href="crear_perfil_manual.php">Nuevo Perfil</a></li>
                <li><a href="perfiles.php">Ver Perfiles</a></li>
            </ul>
        </nav>-->
        <div class="user-dropdown">
            <div class="cont-btn-user" id="close-btn-user">
                <button class="btn-user" id="user-btn">
                    <img src="img/user.png" width="30" alt="User Icon" />
                </button>
            </div>
            <!-- Menu lateral -->
            <div class="dropdown-content" id="dropdown-content">
                <div class="d-flex align-items-center px-3 user-info">
                    <img src="img/user.png" width="40" alt="User Icon" />
                    <div class="div-user">
                        <strong><?php echo $_SESSION['nombre_adm'] . ' ' . $_SESSION['apellidop_adm']; ?></strong><br />
                        <small><?php echo ($_SESSION['correo_adm']) ?></small>
                    </div>
                </div>
                <!-- Nuevas leyendas -->
                <!--         Nombre del chatbot (IXAH), versión 1.0.0, "Desarrollado por Giintape Innovahue" Y correo, soporte@giintapeinnovahueteam.onmicrosoft.com -->
                <div class="dropdown-links">
                    <div class="user-info">
                        <a href="#">Chatbot IXAH</a>
                        <span>Versión 1.0.0</span>
                    </div>
                    <div class="user-info">
                        <a href="vacantes.php">Vacantes</a>
                    </div>
                    <div class="user-info">
                        <a href="candidatos.php">Candidatos</a>
                    </div>
                    <div class="user-info">
                        <a href="informesSSFF.php">Informes SSFF</a>
                    </div>
                    <div class="user-info">
                        <a href="crear_perfil_manual.php">Informe Manual</a>
                    </div>
                    <div class="user-info">
                        <a href="log_errores.php">Errores de los ChatBots</a>
                    </div>
                    <div class="user-info">
                        <a href="#">Desarrollado por Giintape Innovahue</a>
                        <span>Ayuda</span>
                    </div>

                    <div class="user-info">
                        <a href="#" id="sftpLink" data-toggle="modal" data-target="#sftpModal" style="text-decoration: none; color: inherit; display: block; margin-bottom: 10px;">
                            Integración SFTP
                        </a>

                        <a href="https://billing.stripe.com/p/login/fZe3f33cggofeBy144" target="_blank">Actualizar Plan</a>

                    </div>
                    <a class="a1" href="cerrarSesion.php">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>
    <br><br><br>
    <!-- CONTENIDO PRINCIPAL -->
    <main id="contenidoPrincipal" class="container mt-4">

        <h2 class="mb-3">Perfil de Puesto</h2>

        <?php if (!isset($perfil['perfiles']) && !isset($perfil['id'])): ?>
            <div class="alert alert-danger">
                Error al cargar el perfil. Asegúrate de que el backend responda correctamente.
            </div>
        <?php else: ?>

            <div class="card shadow p-4">
                <h4>ID del Perfil: <?php echo $perfil['id']; ?></h4>
                <p><strong>Fuente:</strong> <?php echo htmlspecialchars($perfil['fuente']); ?></p>
                <p><strong>Creado en:</strong> <?php echo htmlspecialchars($perfil['creado_en']); ?></p>

                <hr>

                <h5>Texto Original</h5>
                <p><?php echo nl2br(htmlspecialchars($perfil['texto_original'])); ?></p>

                <hr>

                <button class="btn btn-primary">Mejorar con IA</button>
                <button class="btn btn-success">Publicar</button>
                <button class="btn btn-outline-dark" onclick="window.history.back()">⬅ Regresar</button>

            </div>

        <?php endif; ?>

    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>