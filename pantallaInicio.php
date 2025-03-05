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
                        <img src="img/icono-dia.png" class="img-icono-dia">
                        <div class="container-pers2">
                            <p class="txt-perso-chat">Pantalla de inicio</p>
                            <p>Ayuda a tus clientes a obtener respuestas más rápidas.</p>
                        </div>
                    </div>

                    <div>
                        <label class="label-nombrechat">Mensaje de saludo</label><br>
                        <input type="text" name="inp-saludo" id="inp-saludo"
                            placeholder="¡Saludos! Soy JobHelper, tu guía virtual en el mundo laboral."
                            class="input-saludo" minlength="2" maxlength="80" required><br>

                        <div class="container-conversacion">
                            <div class="asi-conversacion">
                                <label>Temas de conversación</label><br>
                                <ul id="listaTemas">
                                    <li class="tema-item">
                                        <input type="text" name="inp-conversa" class="inp-conversa"
                                            placeholder="Buscar vacantes por categoría" minlength="2" maxlength="28"
                                            required>
                                        <button class="btn-borrar" onclick="eliminarTema(this)">
                                            <img src="img/trash.png" width="20" alt="Delete Topic">
                                        </button>
                                    </li>
                                    <li class="tema-item">
                                        <input type="text" name="inp-conversa" class="inp-conversa"
                                            placeholder="Buscar vacantes por ubicación" required>
                                        <button class="btn-borrar" onclick="eliminarTema(this)">
                                            <img src="img/trash.png" width="20" alt="Delete Topic">
                                        </button>
                                    </li>
                                    <li class="tema-item">
                                        <input type="text" name="inp-conversa" class="inp-conversa"
                                            placeholder="Seguimiento de mi postulación" required>
                                        <button class="btn-borrar" onclick="eliminarTema(this)">
                                            <img src="img/trash.png" width="20" alt="Delete Topic">
                                        </button>
                                    </li>
                                </ul>
                                <div class="container-tema">
                                    <span id="nuevoTema" class="nuevoTema">Añadir tema de conversación</span>
                                    <button onclick="agregarTema()" class="btn-add-conv">
                                        <img src="img/add1.png" width="20" alt="Add Topic">
                                    </button>
                                </div>
                                <!-- Mensaje de notificación -->
                                <p id="errorMensaje" class="errorMensaje" style="color: red; display: none;">No puedes
                                    añadir más de 5 temas.</p>
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
    

</body>

</html>