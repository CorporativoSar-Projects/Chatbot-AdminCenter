<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/sty.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">


    <title>Conversacion</title>
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
        <div class="container-prin-Cr">
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
                    <li class="estas"><a href="crearConversacion.php">Conversación</a></li>
                    <li><a href="pantallaDespedida.php">Despedida</a></li>
                    <li><a href="finalizar.php">Vista previa</a></li>
                </ul>
            </nav>
        </div>


        <div>
            <div class="container-personalizacion">
                <div class="container-pers">
                    <img src="img/paint.png" class="img-paint">
                    <div class="container-pers3">
                        <p class="txt-crea-conv">Crea la conversación</p>
                        <!-- <p>Muéstrales a tus clientes que el chat está aquí para ayudarte.</p> -->
                    </div>
                </div>

                <!-- Añado los dos botones -->
                <div class="botones-conversacion">
                    <input type="button" onclick="impMenu1(event)" id="boton1" class="boton1" value="Tema1"><br>
                    <input type="button" onclick="impMenu2(event)" id="boton2" class="boton1" value="Tema2"><br>
                    <input type="button" onclick="impMenu3(event)" id="boton3" class="boton1" value="Tema3">
                </div>



                <!--Cada boton llama a cada funcion de javascript mas arriba declaradas-->

                <br><br>
                <!-- En el siguiente div se imprimirá el resultado-->
                <div id="imprimir"></div>
                <!-- Cada vez que se pulse un boton, el anterior div se sobreescribirá -->

                <!-- <div class="container-crear-conver">
                        <label class="label-nombrechat2">Escribe el mensaje que verá el usuario al
                            iniciar la conversación</label><br>
                        <input type="text" name="inp-mensaje-usuario" id="inp-mensaje-usuario" placeholder="Escribe"
                            class="inp-mensaje-usuario" required><br>

                        <div class="container-archivo">
                            <label for="archivoLogotipo">Selecciona un archivo</label><br>
                            <div class="select-archivo">
                                <input type="file" id="archivo" accept="" onchange="previsualizarImagen()"
                                    style="display: none;">
                                <button class="btn-select-archivo"
                                    onclick="document.getElementById('archivoLogotipo').click()">
                                    <span id="nombreArchivo" class="nombre-archivo">Seleccione un archivo</span>
                                    <img src="img/add1.png" width="20" alt="Edit ChatBot">
                                </button>
                            </div>
                        </div>

                        <label class="label-nombrechat">Define el nombre de la columna </label><br>
                        <input type="text" name="inp-columna" id="inp-columna" placeholder="Escribe"
                            class="input-columna" required><br>
                    </div>  -->

                <div id="chatbot-toggle" class="chat-toggle">
                    <span id="chatTextBurb" class="chat-text">¡Encuentra Vacantes!</span>
                    <img src="img/chatbot2.png" alt="Chat" class="toggle-icon">
                </div>
            </div>
    </main>

    <!-- jQuery y Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/creaConver.js"></script>
    <script src="js/custom.js"></script>

</body>

</html>