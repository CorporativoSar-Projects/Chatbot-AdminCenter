<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/sty.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
   

    <title>Pantalla Despedida</title>
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

                    <a href="#">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container-prin-Desp">
            <div class="container-bienv">
                <p class="txt-nombre-chat">ChatBot para vacantes</p>
                <div class="container-btn-cerrar-guar">

                    <div class="btn-group">
                        <a href="#" class="btnContinuar" id="btnRegresar"> 
                        <span class="btn-text">Regresar</span>
                        <img src="img/flecha-r.png" class="btn-icon" style="width: 15px;">                    </a>
                    </a>

                    <a href="#" class="btnContinuar" id="btnContinuar">
                        <span class="btn-text">Continuar</span>
                        <img src="img/flecha-c.png" class="btn-icon" style="width: 15px;">
                    </a>
                    </div>
                    <div class="btn-group">
                         <a href="menu.php" class="btnCerrar">
                        <span class="btn-text">Cerrar</span>
                        <img src="img/icons8-close-26.png" class="btn-icon" style="width: 15px;">
                    </a>
                    <button type="submit" id="btnGuardarS" class="btnGuardarS">
                        <span class="btn-text">Guardar y salir</span>
                        <img src="img/icons8-save-24.png" class="btn-icon" style="width: 15px;">
                    </button>
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
                        <li class="estas"><a href="pantallaDespedida.php">Despedida</a></li>
                        <li><a href="finalizar.php">Vista previa</a></li>
                    </ul>
                </nav>
            </div>

            <div>
                <div class="container-personalizacion">
                    <div class="container-pers">
                        <img src="img/icono-dia.png" class="img-icono-dia">
                        <div class="container-despedida">
                            <p class="txt-crea-conv">Mensaje de despedida</p>
                            <!-- <p>Muéstrales a tus clientes que el chat está aquí para ayudarte.</p> -->
                        </div>
                    </div>

                    <div>
                        <label class="label-nombrechat">Escribe el mensaje de despedida </label><br>
                        <input type="text" id="inp-depedida"
                            placeholder="Gracias por usar JobHelper, es un gusto haber podido ayudarte... ¡Hasta la próxima!"
                            class="input-despedida" required><br>

                        

                    </div>

                    <div class="chatbot-principal">
                        <div class="chatbot-container">
                            <div class="chatbot-header" id="chatbot-header">
                                <img src="img/logochiquito.png" alt="Chatbot" class="chatbot-icon">
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
                            <div class="chatbot-content" >
                                <p class="txt-chatbot" id="txt-chatbot-Desp">
                                    Gracias por usar JobHelper, es un gusto haber podido ayudarte... ¡Hasta la próxima!
                                </p>

                                <img src="img/logogiintape.png" alt="" style="margin: 0 auto; display: block; width: 100px; height: auto;"/>
                            </div>
                            <div id="user-input-container" class="user-input-container" >
                                <input type="text" id="user-input" placeholder="Escribe aquí tu respuesta...">
                                <button >Enviar</button>
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
    <script src="js/pantallaDesp.js"></script>
    <script src="js/custom.js"></script>
    

</body>

</html>