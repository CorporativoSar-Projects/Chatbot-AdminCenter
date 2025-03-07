<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/sty.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">

    <title>Finalizar</title>
    <link rel="shortcut icon" href="img/logoPagina.png" />
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
        <div class="container-prinF">
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

                <button type="submit" id="btnGuardar" class="btnGuardarS">
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
                        <li><a href="estilo.php">Estilo</a></li>
                        <li><a href="burbuja.php">Burbuja</a></li>
                        <li><a href="pantallaInicio.php">Mensaje Inicial</a></li>
                        <li><a href="crearConversacion.php">Conversación</a></li>
                        <li><a href="pantallaDespedida.php">Despedida</a></li>
                        <li class="estas"><a href="finalizar.php">Vista previa</a></li>
                    </ul>
                </nav>
            </div>

            <div>
                <div class="container-personalizacion">
                    <div class="container-pers">
                        <img src="img/paint.png" class="img-paint">
                        <div class="container-pers3">
                            <p class="txt-perso-chat">Prueba tu ChatBot </p>
                        </div>
                    </div>


                    <div class="chatbot-principal-desp">
                        <div class="chatbot-container2">
                            <div class="chatbot-header" id="chatbot-header">
                                <img src="img/logochiquito.png" alt="Chatbot" class="chatbot-icon">
                                <p class="txt-titulo-chat" id="txt-titulo-chat">JobHelper</p>
                                <div class="container2">
                                    <div class="chatbot-min" title="Minimizar" onclick="toggleChatbot()">
                                        <img src="img/line.png" />
                                    </div>
                                    <div class="chatbot-close" title="Cerrar" onclick="cerrar()">
                                        <img src="img/close.png" />
                                    </div>
                                </div>
                            </div>
                            <div class="chatbot-content2">
                                <p class="txt-chatbot">
                                    ¡Saludos! Soy JobHelper, tu guía virtual en el mundo laboral.
                                    Mi misión es facilitarte el buscar la mejor opción.
                                </p>
                                <div class="chatbot-buttons2">
                                    <button class="chatbot-button2">Buscar vacantes por categoría</button>
                                    <button class="chatbot-button2">Buscar vacantes por ubicación</button>
                                    <button class="chatbot-button2">Seguimiento de mi postulación</button>
                                </div>
                            </div>
                            <div id="user-input-container" class="user-input-container">
                                <input type="text" id="user-input" placeholder="Escribe aquí tu respuesta...">
                                <button>Enviar</button>
                            </div>
                        </div>
                        <div class="link-func">
                            <div class="label-func">
                                <label for="input">URL de funcionamiento</label>
                            </div>
                            <div class="container-input">
                                <input type="text" id="input" class="txtfunc">
                                    <button type="submit" id="myBtn" class="btnGenerar">
                                        <span class="btn-text-Generar">Generar</span>
                                    <!--  <img src="img/icons8-link-24.png" class="btn-icon" style="width: 20px;">-->
                                    </button>
                            </div>
                        </div>
                    </div>

                </div>

                <div id="myModal" class="modal">

                    
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5>¡Copia el link de tu ChatBot!</h5>
                            <span class="close">&times;</span>
                        </div>
                        <div class="modal-body">
                            <p>Link</p>
                            <input type="text" name="inp-nombre" id="inp-link"
                                value="https://web-chat.naquistristiquevitaeenim " class="input-link">
                                <button class="copy-button" onclick="copyLink()">Copiar</button>
                        </div>
                        <div class="modal-footer close-footer">
                            <h6>Cerrar</h6>
                        </div>
                    </div>

                </div>

            </div>

           
    </main>

   

    <!-- jQuery y Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/custom.js"></script>

    <script src="js/link.js"></script>
   

</body>

</html>