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


    <title>Pantalla Despedida</title>
    <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
</head>

<body>
    <div class="rectangulo-container">
        <a href="menu.php">
            <img src="img/LOGOTIPO_IXAH-02.png" width="70px" alt="Logo" class="img-logo-chiq">
        </a>
    </div>

    <header>
        <?php include 'DatosMenu.php'; ?>
    </header>

    <main id="contenidoPrincipal">
        <div class="container-prin-Desp">
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
                        <button type="submit" id="btnGuardarDespedida" class="btnGuardarS" onclick="guardarChatbotCompleto()">
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
                            <p>Muéstrales a tus clientes que el mensaje de despedida también está pensado para ayudarles.</p>
                        </div>
                    </div>

                    <div style="position: relative; width: 100%;">
                        <label class="label-nombrechat">Mensaje de despedida </label><br>
                        <textarea id="inp_despedida"
                            placeholder="Gracias por usarme, me dio mucho gusto poder ayudarte... ¡Hasta la próxima!"
                            class="input-despedida" required maxlength="280"><?php echo htmlspecialchars($chatbot['inp_despedida'] ?? ''); ?></textarea><br>
                        <span id="contadorCaracteres">
                            0 / 280
                        </span>
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
                            <div class="chatbot-content1">
                                <p class="txt-chatbot" id="txt-chatbot-Desp">

                                    <?php
                                    $despedida = !empty($chatbot['inp_despedida'])
                                        ? $chatbot['inp_despedida']
                                        : 'Gracias por usarme, me dio mucho gusto poder ayudarte... ¡Hasta la próxima!';
                                    echo htmlspecialchars($despedida);
                                    ?>
                                </p>
                                <?php
                                //$logodes = (!empty($chatbot['urlLogotipo'])) ? $chatbot['urlLogotipo'] : 'img/Logo_principal.svg';
                                ?>
                                <!--<img src="<?php echo htmlspecialchars($logodes); ?>" alt=""  />-->

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
    <script src="js/pantallaDesp.js"></script>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css"/>
    <link rel="stylesheet" href="css/tour_ixah.css">
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>
    <script src="js/tour_ixah.js"></script> 

</body>

</html>