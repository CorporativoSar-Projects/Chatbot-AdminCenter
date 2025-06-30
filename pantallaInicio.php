<?php

session_start();

if (!isset($_SESSION['id_adm'])) {
    session_destroy();
    header("location: ./index.php?error=2");
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/sty.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Pantalla Inicio</title>
    <link rel="shortcut icon" href="logoPagina.png" />
</head>

<body>
    <div class="rectangulo-container">
        <img src="img/newLogo.svg" width="70px" alt="Logo" class="img-logo-chiq">
    </div>

    <header>
        <div class="user-dropdown">
            <div class="cont-btn-user" id="close-btn-user">
                <button class="btn-user" id="user-btn"><img src="img/user.png" width="30" alt="User Icon"></button>
            </div>
            <!-- Menu lateral -->
            <div class="dropdown-content" id="dropdown-content">
                <div class="d-flex align-items-center px-3 user-info">
                    <img src="img/user.png" width="40" alt="User Icon">
                    <div class="div-user">
                        <strong><?php echo $_SESSION['nombre_adm'] . ' '. $_SESSION['apellidop_adm']; ?></strong><br>
                        <small><?php echo ($_SESSION['correo_adm']) ?></small>
                    </div>
                </div>

                <div class="dropdown-links">
                    <div class="user-info">
                        <a href="#">Chatbot IXAH</a>
                        <span>Versión 1.0.0</span>
                    </div>
                    <div class="user-info">
                        <a href="#">Desarrollado por Giintape Innovahue</a>
                        <span> soporte@giintapeinnovahueteam.onmicrosoft.com</span>
                    </div>

                    <a href="cerrarSesion.php">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container-prin-PI">
            <div class="container-bienv">
                <p class="txt-nombre-chat">ChatBot para vacantes</p>
                <div class="container-btn-cerrar-guar">

                    <div class="btn-group">
                        <a href="#" class="btnContinuar" id="btnRegresar">
                            <span class="btn-text">Regresar</span>
                            <img src="img/flecha-r.png" class="btn-icon" style="width: 15px;">
                        </a>

                        <a href="#" class="btnContinuar" id="btnContinuar">
                            <span class="btn-text">Continuar</span>
                            <img src="img/flecha-c.png" class="btn-icon" style="width: 15px;">
                        </a>
                    </div>
                    <div class="btn-group">
                        <button type="submit" id="btnGuardarMensaje" class="btnGuardarS">
                            <span class="btn-text">Guardar</span>
                            <img src="img/icons8-save-24.png" class="btn-icon" style="width: 15px;">
                        </button>
                        <a href="menu.php" class="btnCerrar" id="btnCerrar">
                            <span class="btn-text">Salir</span>
                            <img src="img/icons8-close-26.png" class="btn-icon" style="width: 15px;">
                        </a>
                    </div>


                </div>

            </div>

            <div class="container-menu-pers">
                <nav class="menu-pers">
                    <ul>
                        <li><a href="estilo.php">Estilo</a></li>
                        <li><a href="burbuja.php">Burbuja</a></li>
                        <li class="estas"><a href="pantallaInicio.php">Mensaje Inicial</a></li>
                        <li><a href="crearConversacion.php">Conversación</a></li>
                        <li><a href="pantallaDespedida.php">Despedida</a></li>
                        <li><a href="finalizar.php">Vista previa</a></li>
                    </ul>
                </nav>
            </div>

            <div>
                <div class="container-personalizacion">
                    <div class="container-pers">
                        <img src="img/icono-dia.png" class="img-icono-dia2">
                        <div class="container-pers2">
                            <p class="txt-perso-chat">Pantalla de inicio</p>
                            <p class="msg-perso-chat">Ayuda a tus clientes a obtener respuestas más rápidas.</p>
                        </div>
                    </div>

                    <div>
                        <label class="label-nombrechat">Mensaje inicial</label><br>
                        <textarea type="text" name="inp_saludo" id="inp_saludo"
                            placeholder="¡Saludos! Soy JobHelper, tu guía virtual en el mundo laboral."
                            class="input-saludo" minlength="2" maxlength="70" required></textarea> <br>

                        <div class="container-conversacion">
                            <div class="asi-conversacion">
                                <label>Temas de conversación</label><br>
                                <!-- Se modifico el limite de caracterés a 33 de los inputs de conversación  -->
                                <ul id="listaTemas">
                                    <li class="tema-item">
                                        <input type="text" name="inp-conversa" class="inp-conversa" id="inp_conversa1"
                                            value="Buscar vacantes por categoría" minlength="2" maxlength="33"
                                            readonly disabled>
                                    </li>
                                    <li class="tema-item">
                                        <input type="text" name="inp-conversa" class="inp-conversa" id="inp_conversa2"
                                            value="Buscar vacantes por ubicación" minlength="2" maxlength="33" readonly disabled>
                                    </li>
                                    <li class="tema-item">
                                        <input type="text" name="inp-conversa" class="inp-conversa" id="inp_conversa3"
                                            value="Seguimiento de mi postulación" minlength="2" maxlength="33" readonly disabled>
                                    </li>
                                </ul>
                                <!-- Mensaje de notificación -->
                                <p id="errorMensaje" class="errorMensaje" style="color: red; display: none;">No puedes
                                    añadir más de 5 temas.</p>
                            </div>
                        </div>

                    </div>

                    <div class="chatbot-principal">
                        <div class="chatbot-container">
                            <div class="chatbot-header" id="chatbot-header">
                                <img src="img/Logo_cabeza.svg" alt="Chatbot" class="chatbot-icon" id="logoPreview">
                                <p class="txt-titulo-chat" id="txt-titulo-chat">JobHelper</p>
                                <div class="container1">
                                    <div class="chatbot-min" title="Minimizar" onclick="toggleChatbot()">
                                        <img src="img/line.png" />
                                    </div>
                                    <div class="chatbot-close" title="Cerrar" onclick="cerrar()">
                                        <img src="img/close.png" />
                                    </div>
                                </div>
                            </div>
                            <div class="chatbot-content">
                                <p class="txt-chatbot" id="txt-chatbot">
                                    ¡Saludos! Soy JobHelper, tu guía virtual en el mundo laboral.
                                    Mi misión es facilitarte el buscar la mejor opción.
                                </p>
                                <div class="chatbot-buttons" id="chatbot-buttons">
                                    <!-- Botones del chatbot se agregarán aquí -->
                                </div>
                            </div>
                            <div id="user-input-container" class="user-input-container">
                                <input type="text" id="user-input" placeholder="Escribe aquí tu respuesta...">
                                <button>Enviar</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>



    <!-- jQuery y Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/pantallaIn.js"></script>
    <script src="js/custom.js"></script>
    <script src="js/guardar.js"></script>
    <script src="js/menuLateral.js" type="module"></script>


</body>

</html>