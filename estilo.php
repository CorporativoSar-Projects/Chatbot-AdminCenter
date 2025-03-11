<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/sty.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Estilo</title>
    <link rel="shortcut icon" href="logoPagina.png" />
</head>

<body>

    <div class="rectangulo-container">
        <img src="img/logochiquito.png" width="70px" alt="Logo" class="img-logo-chiq">
    </div>
    <header>
        <div class="user-dropdown">
            <div class="cont-btn-user">
                <button class="btn-user"><img src="img/user.png" width="30" alt="User Icon"></button>
            </div>
            <div class="dropdown-content">
                <div class="d-flex align-items-center px-3 user-info">
                    <img src="img/user.png" width="40" alt="User Icon">
                    <div class="div-user">
                        <strong>Karla Durán</strong><br>
                        <small>kduranc@giint...</small>
                    </div>
                </div>
                <div class="dropdown-links">
                    <div class="user-info">
                        <a href="#">ChatBot para vacantes</a>
                        <span>Plan Básico Mensual</span>
                    </div>
                    <div class="user-info">
                        <a href="#">ChatBot para pedidos</a>
                        <span>Plan Básico 3 Meses</span>
                    </div>
                    <a href="cerrarSesion.php">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container-prin">
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
                        <button type="submit" id="btnGuardarS" class="btnGuardarS">
                            <span class="btn-text">Guardar</span>
                            <img src="img/icons8-save-24.png" class="btn-icon" style="width: 15px;">
                        </button>

                        <a href="menu.php" class="btnCerrar">
                            <span class="btn-text">Salir</span>
                            <img src="img/icons8-close-26.png" class="btn-icon" style="width: 15px;">
                        </a>
                    </div>
                </div>
            </div>

            <div class="container-menu-pers">
                <nav class="menu-pers">
                    <ul>
                        <li class="estas"><a href="estilo.php">Estilo</a></li>
                        <li><a href="burbuja.php">Burbuja</a></li>
                        <li><a href="pantallaInicio.php">Mensaje Inicial</a></li>
                        <li><a href="crearConversacion.php">Conversación</a></li>
                        <li><a href="pantallaDespedida.php">Despedida</a></li>
                        <li><a href="finalizar.php">Vista previa</a></li>
                    </ul>
                </nav>
            </div>

            <div>
                <div class="container-personalizacion">
                    <div class="container-pers">
                        <img src="img/paint.png" class="img-paint">
                        <div class="container-pers2">
                            <p class="txt-perso-chat">Personaliza la interfaz de usuario del chat</p>
                            <p>Actualiza el estilo para que coincida con tu marca y tu sitio web.</p>
                        </div>
                    </div>

                    <div>
                        <label class="label-nombrechat">Nombre visible de tu ChatBot</label><br>
                        <input type="text" name="inp-nombre" id="inp-nombre" placeholder="JobHelper"
                            class="input-nombre" minlength="2" maxlength="10" required><br>

                        <div class="container-colors">
                            <div class="nombre-colord">
                                <label for="colorPrimario">Color Primario</label><br>
                                <div class="color-selector">
                                    <input type="color" id="colorPrimario" value="#e39842"
                                        oninput="actualizarColores()">
                                    <div id="muestraColorPrimario" class="color-circle"></div><br>
                                </div>

                                <label for="colorAcento">Color de Acento</label><br>
                                <div class="color-selector">
                                    <input type="color" id="colorAcento" value="#383838" oninput="actualizarColores()">
                                    <div id="muestraColorAcento" class="color-circle"></div><br>
                                </div>
                            </div>
                            <div class="nombre-colorc">
                                <label for="colorSecundario">Color Secundario</label><br>
                                <div class="color-selector">
                                    <input type="color" id="colorSecundario" value="#b6b6b6"
                                        oninput="actualizarColores()">
                                    <div id="muestraColorSecundario" class="color-circle"></div><br>
                                </div>

                                <label for="colorTexto">Color de Texto</label><br>
                                <div class="color-selector">
                                    <input type="color" id="colorTexto" value="#000000" oninput="actualizarColores()">
                                    <div id="muestraColorTexto" class="color-circle"></div><br>
                                </div>
                            </div>

                            <!-- Contenedor para cargar la imagen con icono adjunto -->
                            <div class="container-archivo">
                                <label for="archivoLogotipo">Ingresa Logotipo</label><br>
                                <div class="select-archivo">
                                    <input type="file" id="archivoLogotipo" accept="image/*"
                                        onchange="previsualizarImagen()" style="display: none;">
                                    <button class="btn-select-archivo"
                                        onclick="document.getElementById('archivoLogotipo').click()">
                                        <span id="nombreArchivo" class="nombre-archivo">Seleccione un archivo</span>
                                        <img src="img/add1.png" width="20" alt="Edit ChatBot">
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chatbot-principal">
                        <div class="chatbot-container">
                            <div class="chatbot-header" id="chatbot-header">
                                <img src="img/logochiquito.png" alt="Chatbot" class="chatbot-icon">
                                <p class="txt-titulo-chat" id="txt-titulo-chat">JobHelper</p>
                                <div class="container1">
                                    <div class="chatbot-min" title="Minimizar" onclick="toggleChatbot()">
                                        <img src="https://chatbot.giintapeinnovahue.com/img/line.png" />
                                    </div>
                                    <div class="chatbot-close" title="Cerrar" onclick="cerrar()">
                                        <img src="https://chatbot.giintapeinnovahue.com/img/close.png" />
                                    </div>
                                </div>
                            </div>
                            <div class="chatbot-content">
                                <p class="txt-chatbot">
                                    ¡Saludos! Soy JobHelper, tu guía virtual en el mundo laboral.
                                    Mi misión es facilitarte el buscar la mejor opción.
                                </p>
                                <div class="chatbot-buttons">
                                    <button class="chatbot-button">Buscar vacantes por categoría</button>
                                    <button class="chatbot-button">Buscar vacantes por ubicación</button>
                                    <button class="chatbot-button">Seguimiento de mi postulación</button>
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
    </main>

    <!-- jQuery y Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/estilo.js"></script>
    <script src="js/custom.js"></script>

</body>

</html>
