<?php

include('modelo/obtenerDatos.php');
include 'modelo/consultas_menu.php';


//$id_chatbot = $_SESSION['id_chatbot'] ?? null;
$esEdicion = !empty($id_chatbot);

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/sty.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Burbuja</title>
    <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
</head>

<body>

    <div class="rectangulo-container">
        <a href="menu.php">
            <img src="img/LOGOTIPO_IXAH-02.png" width="70px" alt="Logo" class="img-logo-chiq">
        </a>
    </div>
    <header>
        <!-- <nav class="navbar">
            <ul class="filas">
                <li><a href="#" class="txt-home">Home</a></li>
                <li><a href="#">ChatBot para vacantes</a>
                    <ul>
                        <li><a href="#">Chatbot para pedidos</a></li>
                        <li class="divider"></li>
                        <li><a href="#">Crear nuevo Chatbot</a></li>
                    </ul>
                </li>
            </ul>
        </nav> -->

        <?php include 'DatosMenu.php'; ?>
    </header>

    <main id="contenidoPrincipal">
        <div class="container-prinBur">
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
                            <img src="img/flecha-c.png" class="btn-icon" style="width: 17px;">
                        </a>
                    </div>
                    <div class="btn-group">
                        <button type="submit" id="btnGuardarBurbuja" class="btnGuardarS" onclick="guardarChatbotCompleto()">
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
                        <li class="estas"><a href="burbuja.php">Burbuja</a></li>
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
                        <img src="img/icono-dia.png" class="img-icono-dia">
                        <div class="container-pers2">
                            <p class="txt-perso-chat3">Personaliza el mensaje de la burbuja</p>
                            <p class="txt-msg-chat3">Muéstrales a tus clientes que el chat está para ayudarles.</p>
                        </div>
                    </div>

                    <div>

                        <div style="position: relative; width: 100%;">
                            <label class="label-nombrechat">Mensaje</label><br>
                            <input type="text" name="inp_burbuja" id="inp_burbuja" placeholder="¡Encuentra vacantes!"
                                class="input-burbuja" minlength="2" maxlength="20" required value="<?php echo htmlspecialchars($chatbot['inp_burbuja'] ?? ''); ?>"><br>
                            <span id="contadorBurbu" class="contador">
                                0 / 20
                            </span>
                        </div>

                        <div class="container-colors">
                            <div class="nombre-colord">
                                <label for="colorPrimarioBurbuja">Color Primario</label><br>
                                <div div class="color-selector">
                                    <input type="color" id="colorPrimarioBurbuja" value="<?php echo htmlspecialchars($chatbot['colorPrimario'] ?? '#3ca6e5'); ?>"
                                        oninput="actualizarColores()">
                                    <div id="muestraColorPrimarioBurbuja" class="color-circle"></div><br>

                                </div>
                            </div>
                            <div class="nombre-colorc">
                                <label for="colorTextoBurbuja">Color de texto</label><br>
                                <div class="color-selector">
                                    <input type="color" id="colorTextoBurbuja" value="<?php echo htmlspecialchars($chatbot['colorTexto'] ?? '#000000'); ?>"
                                        oninput="actualizarColores()">
                                    <div id="muestraColorTextoBurbuja" class="color-circle"></div><br>
                                </div>
                            </div>


                        </div>

                        <!-- Burbuja del chatbot -->
                        <div id="chatbot-toggle" class="chat-toggle">
                            <span id="chatTextBurb" class="chat-text"><?php echo !empty($chatbot['inp_burbuja']) ? htmlspecialchars($chatbot['inp_burbuja']) : '¡Encuentra Vacantes!'; ?></span>
                            <?php
                            $logo = (!empty($chatbot['urlLogotipo'])) ? $chatbot['urlLogotipo'] : 'img/Logo_cabeza.svg';
                            ?>
                            <img id="chatBubbleIcon" src="<?php echo htmlspecialchars($logo); ?>" alt="Chat" class="toggle-icon">
                        </div>


                    </div>

                </div>
    </main>
    <?php include 'modalIntegracion.php'; ?>
    <!-- jQuery y Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/burbuja.js"></script>
    <script>
        const id_chatbot = <?php echo json_encode($id_chatbot); ?>;
        if (id_chatbot) {
            localStorage.setItem("id_chatbot", id_chatbot);
        }
    </script>
    <script src="js/custom.js"></script>
    <script src="js/menuLateral.js" type="module"></script>
    <script src="js/guardar.js"></script>
    <script src="js/formularioIntegracion.js"></script>
    <script>
        window.appData = {
            nombrePlan: '<?php echo $planUsuario; ?>',
            estadoSuscripcion: '<?php echo $estadoSuscripcion; ?>',
            sftpActivo: <?php echo $sftpActivo; ?>,
            sftpConfig: <?php
                        echo json_encode([
                            'tipo_integracion' => $sftpData['tipo_integracion'] ?? 'estandar',
                            'activo' => isset($sftpData['activo']) ? (int)$sftpData['activo'] : 0,
                            'servidor' => $sftpData['servidor'] ?? '',
                            'puerto' => $sftpData['puerto'] ?? '22',
                            'usuario' => $sftpData['usuario'] ?? '',
                            'contrasena' => '',
                            'rutaDestino' => $sftpData['rutaDestino'] ?? '',
                            'url_estandar' =>  $sftpData['url_estandar'] ?? ''
                        ]);
                        ?>
        };
    </script>
    <!-- <script>
        window.IXAH_CONFIG = {
            tieneChatbot: <?php echo $esEdicion ? 'true' : 'false'; ?>,
            modoCreacion: <?php echo $esEdicion ? 'false' : 'true'; ?>
        };
    </script>-->
    <script src="js/guardadoGeneral.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css"/>
    <link rel="stylesheet" href="css/tour_ixah.css">
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>
    <script src="js/tour_ixah.js"></script> 


</body>

</html>