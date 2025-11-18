<?php

include('modelo/obtenerDatos.php')

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/sty.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>Finalizar</title>
    <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
</head>

<body>

    <div class="rectangulo-container">
        <img src="img/Logo_cabeza.svg" width="70px" alt="Logo" class="img-logo-chiq">
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
                        <strong><?php echo $_SESSION['nombre_adm'] . ' ' . $_SESSION['apellidop_adm']; ?></strong><br>
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
                        <span> Ayuda</span>
                    </div>

                    <a class="a1" href="cerrarSesion.php">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <div class="container-prinF">
            <div class="container-bienv">
                <p class="txt-nombre-chat">ChatBot</p>
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

                        <button type="submit" id="btnGuardarFinalizar" class="btnGuardarS">
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
                                <?php
                                $logo = (!empty($chatbot['urlLogotipo'])) ? $chatbot['urlLogotipo'] : 'img/Logo_cabeza.svg';
                                ?>
                                <img src="<?php echo htmlspecialchars($logo); ?>" alt="Chatbot" class="chatbot-icon" id="logoPreview">
                                <p class="txt-titulo-chat" id="txt-titulo-chat"><?php echo htmlspecialchars($chatbot['inp_nombre'] ?? 'IXAH'); ?></p>
                                <div class="container2">
                                    <div class="chatbot-min" title="Minimizar" onclick="toggleChatbot()">
                                        <svg class="icono-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M 6 12 C 6 11.449219 6.449219 11 7 11 L 17 11 C 17.550781 11 18 11.449219 18 12 C 18 12.550781 17.550781 13 17 13 L 7 13 C 6.449219 13 6 12.550781 6 12 Z" />
                                        </svg>
                                    </div>
                                    <div class="chatbot-close" title="Cerrar" onclick="cerrar()">
                                        <svg class="icono-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="miter">
                                            <path d="M 16 8 L 8 16 M 8 8 L 16 16" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="chatbot-content2">
                                <p class="txt-chatbot" id="txt-chatbot">
                                    <?php
                                    $saludo = !empty($chatbot['inp_saludo'])
                                        ? $chatbot['inp_saludo']
                                        : '¡Hola! Soy IXAH, tu asistente virtual en el mundo laboral. ¿En qué te puedo ayudar hoy?';
                                    echo htmlspecialchars($saludo);
                                    ?>
                                </p>
                                <div class="chatbot-buttons2">
                                    <button class="chatbot-button2">Buscar vacantes por categoría</button>
                                    <button class="chatbot-button2">Buscar vacantes por ubicación</button>
                                    <button class="chatbot-button2">Seguimiento de mi postulación</button>
                                </div>
                            </div>

                            <!--Contenedor para respuesta del usuario-->
                            <div class="user-message2">
                                <p>vurzolakku@gufum.com</p>
                            </div>


                            <div id="user-input-container" class="user-input-container">
                                <input type="text" id="user-input" placeholder="Escribe aquí tu respuesta...">
                                <button>Enviar</button>
                            </div>
                        </div>
                        <?php
                        // Obtener la URL actual del chatbot (si existe)
                        $urlFuncionamiento = $chatbot['url_cs_emp'] ?? '';
                        $esEditable = empty($urlFuncionamiento);
                        ?>

                        <div class="link-func">
                            <div class="label-func">
                                <label for="input">URL de funcionamiento</label>
                            </div>

                            <div class="container-input">
                                <input
                                    type="text"
                                    id="input"
                                    class="txtfunc"
                                    name="url_cs_emp"
                                    value="<?php echo htmlspecialchars($urlFuncionamiento); ?>"
                                    <?php echo $esEditable ? '' : 'readonly disabled'; ?>
                                    onfocus="if(this.hasAttribute('readonly')) this.blur();"
                                    style="<?php echo $esEditable ? '' : 'pointer-events:none; user-select:none; background-color:#f8f9fa; cursor:not-allowed;'; ?>"
                                    placeholder="<?php echo $esEditable ? 'Ejemplo: https://tusitio.com/chatbot' : ''; ?>">

                                <button type="submit" class="btnGuardarurl" id="btnGuardarurl">
                                    <i class="fas fa-save"></i>
                                </button>
                            </div>
                            <div class="generar-container">
                                <button type="submit" id="myBtn" class="btnGenerar" data-idadm="<?= $_SESSION['id_adm'] ?>" data-toggle="modal" data-target="#myModal">
                                    <span class="btn-text-Generar">Generar</span>
                                </button>

                                <!-- Nuevo botón a la derecha -->
                                <button type="button" id="btnInteractivo" class="btnInteractivo" onclick="window.location.href='pagina_pruebas.php'">
                                    <span class="btnInteractivo">Modo Interactivo</span>
                                </button>
                            </div>

                        </div>

                    </div>
                </div>


                <!-- Modal -->
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="linkModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">

                            <!-- Header -->
                            <div class="modal-header">
                                <h5 class="modal-title" id="linkModalLabel">¡Copia el código HTML de tu ChatBot!</h5>

                                <div class="d-flex align-items-center" style="margin-left: 25px; cursor: pointer;" id="copyWrapper">
                                    <i class="fas fa-copy" id="copySnippetBtn" style="font-size: 1.5rem;" title="Copiar Código"></i>
                                    <span style="margin-left: 6px; font-weight: 500;">Copiar</span>
                                </div>

                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                    <span aria-hidden="true">&times;</span>
                                </button>

                            </div>


                            <div class="modal-body">

                                <p><strong>Instrucciones:</strong> Copia y pega este código en tu sitio web <code>&lt;/body&gt;</code>.</p>
                                <pre><code id="snippetCode"></code></pre>
                                <div id="alertContainer"></div>



                            </div>


                            <!-- Footer -->
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                <!--- <button type="button" class="btn btn-success" onclick="copySnippet()">Copiar Código</button>--->
                            </div>

                        </div>
                    </div>
                </div>

            </div>


    </main>



    <!-- jQuery y Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/link.js"></script>
    <script src="js/custom.js"></script>
    <script src="js/menuLateral.js" type="module"></script>
    <script src="js/guardar.js"></script>
    <script src="js/urlFuncionamiento.js"></script>

</body>

</html>