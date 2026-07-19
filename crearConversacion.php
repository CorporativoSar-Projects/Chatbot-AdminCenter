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


    <title>Conversacion</title>
    <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
    <!-- Tour Ixah plus-->
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>
<link rel="stylesheet" href="temp_tour_base/tour_ixah.css"/>
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
        <div class="container-prin-Cr">
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
                        <button type="submit" id="btnGuardarConver" class="btnGuardarS" onclick="guardarChatbotCompleto()">
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
                            <p>Muéstrales a tus clientes que la conversación está diseñada para guiarlos.</p>
                        </div>
                    </div>

                    <!-- Añado los dos botones -->
                    <div class="botones-conversacion">
                        <input type="button" onclick="impMenu1(event)" id="boton1" class="boton1" value="Buscar vacantes por categoría"><br>
                        <input type="button" onclick="impMenu2(event)" id="boton2" class="boton1" value="Buscar vacantes por ubicación"><br>
                        <input type="button" onclick="impMenu3(event)" id="boton3" class="boton1" value="Seguimiento de mi postulación">
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

                        <label class="label-nombrechat">Link de referencia</label><br>
                        <input type="url" name="inp-link-referencia" id="inp-link-referencia" placeholder="https://ejemplo.com"
                             class="input-columna-crear"><br>
                    </div>  -->

    </main>

    <!--Modal de integración -->
    <?php include 'modalIntegracion.php'; ?>

    <!-- jQuery y Bootstrap JavaScript -->
    <script>
        // Pasamos los datos PHP a un objeto JS llamado datosChatbot
        const datosChatbot = <?php echo json_encode($chatbot); ?>;
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/creaConver.js"></script>

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

    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
    <script src="temp_tour_base/tour_ixah.js"></script>
    <?php include 'guiaFlotante.php'; ?>

</body>

</html>