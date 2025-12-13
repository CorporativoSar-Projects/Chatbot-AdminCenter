<?php


include('modelo/obtenerDatos.php');
include 'modelo/consultas_menu.php';

$id_chatbot = $_SESSION['id_chatbot'] ?? null;
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
    <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
</head>

<body>
    <div class="rectangulo-container">
        <a href="menu.php">
            <img src="img/LOGOTIPO_IXAH-02.png" width="70px" alt="Logo" class="img-logo-chiq">
        </a>
    </div>

    <header>
        <!-- Menu lateral -->
        <?php include 'DatosMenu.php'; ?>
    </header>

    <main id="contenidoPrincipal">
        <div class="container-prin-PI">
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
                        <button type="submit" id="btnGuardarMensaje" class="btnGuardarS" onclick="guardarChatbotCompleto()">
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
                        <div style="position: relative; width: 45%;">
                            <label class="label-nombrechat">Mensaje de saludo</label><br>
                            <textarea name="inp_saludo" id="inp_saludo"
                                placeholder=" ¡Hola! Soy IXAH, tu asistente virtual en el mundo laboral. ¿En qué te puedo ayudar hoy?"
                                class="input-saludo" minlength="2" maxlength="180"
                                style="width: 100%; padding-bottom: 20px;"><?php echo htmlspecialchars($chatbot['inp_saludo'] ?? ''); ?></textarea>

                            <span id="contadorCaracteres"
                                style="position: absolute; bottom: 5px; right: 10px; font-size: 12px; color: gray;">
                                0 / 180
                            </span>
                        </div>

                        <div class="container-conversacion">
                            <div class="asi-conversacion">
                                <label>Temas de conversación</label><br>
                                <!-- Se modifico el limite de caracterés a 33 de los inputs de conversación  -->
                                <ul id="listaTemas">
                                    <li class="tema-item">
                                        <input type="text" name="inp-conversa" class="inp-conversa" id="inp_conversa1"
                                            value="<?php echo htmlspecialchars(!empty($chatbot['inp_conversa1']) ? $chatbot['inp_conversa1'] : 'Buscar vacantes por categoría'); ?>" minlength="2" maxlength="33"
                                            readonly disabled>
                                    </li>
                                    <li class="tema-item">
                                        <input type="text" name="inp-conversa" class="inp-conversa" id="inp_conversa2"
                                            value="<?php echo htmlspecialchars(!empty($chatbot['inp_conversa2']) ? $chatbot['inp_conversa2'] : 'Buscar vacantes por ubicación'); ?>" minlength="2" maxlength="33" readonly disabled>
                                    </li>
                                    <li class="tema-item">
                                        <input type="text" name="inp-conversa" class="inp-conversa" id="inp_conversa3"
                                            value="<?php echo htmlspecialchars(!empty($chatbot['inp_conversa3']) ? $chatbot['inp_conversa3'] : 'Seguimiento de mi postulación'); ?>" minlength="2" maxlength="33" readonly disabled>
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
                                <?php
                                $logo = (!empty($chatbot['urlLogotipo'])) ? $chatbot['urlLogotipo'] : 'img/Logo_cabeza.svg';
                                ?>
                                <img src="<?php echo htmlspecialchars($logo); ?>" alt="Chatbot" class="chatbot-icon" id="logoPreview">
                                <p class="txt-titulo-chat" id="txt-titulo-chat"><?php echo htmlspecialchars($chatbot['inp_nombre'] ?? 'IXAH'); ?></p>
                                <div class="container1">
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
                            <div class="chatbot-content">
                                <p class="txt-chatbot" id="txt-chatbot">
                                    <?php
                                    $saludo = !empty($chatbot['inp_saludo'])
                                        ? $chatbot['inp_saludo']
                                        : ' ¡Hola! Soy IXAH, tu asistente virtual en el mundo laboral. ¿En qué te puedo ayudar hoy?';
                                    echo htmlspecialchars($saludo);
                                    ?>

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

    <!--Modal de integración -->
    <?php include 'modalIntegracion.php'; ?>


    <!-- jQuery y Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/pantallaIn.js"></script>
    <script>
        const id_chatbot = <?php echo json_encode($id_chatbot); ?>;
        if (id_chatbot) {
            localStorage.setItem("id_chatbot", id_chatbot);
        }
    </script>
    <script src="js/custom.js"></script>
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
    <script src="js/guardadoGeneral.js"></script>
    <script src="js/menuLateral.js" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css" />
    <link rel="stylesheet" href="css/tour_ixah.css">
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>
    <script src="js/tour_ixah.js"></script>



</body>

</html>