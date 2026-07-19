<?php
include('modelo/obtenerDatos.php');
include 'modelo/conexion_bd.php';
# include 'modelo/consultas_menu.php';


$id_adm = $_SESSION['id_adm'];
$sql = "SELECT COUNT(*) AS total FROM chatbot WHERE Administrador_id_adm = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_adm);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['total'] >= 1 && isset($_GET['nuevo'])) {
    echo "<script>
      Swal.fire({
        icon: 'info',
        title: 'Límite alcanzado',
        text: 'Solo puedes crear un chatbot por empresa.',
        confirmButtonColor: '#3ca6e5'
      }).then(() => {
        window.location.href = 'menu.php';
      });
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/sty.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Estilo</title>
    <link rel="shortcut icon" href="img/Logo_cabeza.svg"/>
    <!-- TOUR Ixah plus-->
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

    <main>
        <div class="container-prin">
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
                        <button type="submit" id="btnGuardarEstilo" class="btnGuardarS" onclick="guardarChatbotCompleto()">
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
                            <p class="txt-perso-chat2">Personaliza la interfaz de usuario del chat</p>
                            <p class="msg-perso-chat2">Actualiza el estilo para que coincida con tu marca y tu sitio web.</p>
                        </div>
                    </div>

                    <div>
                        <div style="position: relative; width: 100%;">
                        <label class="label-nombrechat">Nombre visible de tu ChatBot</label><br>
                        <input type="text" name="inp_nombre" id="inp_nombre" placeholder="IXAH"
                            class="input-nombre" minlength="2" maxlength="10" required value="<?php echo htmlspecialchars($chatbot['inp_nombre'] ?? ''); ?>">
                            <br>
                            <span id="contadorSty"  class="contador" >
                                0 / 10
                            </span>
                        </div>

                        <div class="container-colors">
                            <div class="nombre-colord">
                                <label for="colorPrimario">Color Primario</label><br>
                                <div class="color-selector">
                                    <input type="color" id="colorPrimario"  value="<?php echo htmlspecialchars($chatbot['colorPrimario'] ?? '#3ca6e5'); ?>"
                                        oninput="actualizarColores()">
                                    <div id="muestraColorPrimario" class="color-circle"></div><br>
                                </div>

                                <label for="colorAcento">Color de Acento</label><br>
                                <div class="color-selector">
                                    <input type="color" id="colorAcento" value="<?php echo htmlspecialchars($chatbot['colorAcento'] ?? '#383838' ); ?>" oninput="actualizarColores()">
                                    <div id="muestraColorAcento" class="color-circle"></div><br>
                                </div>

                                <label for="colorUsuario">Respuesta Usuario</label><br>
                                <div class="color-selector">
                                    <input type="color" id="colorRespuestaUsuario" value="<?php echo htmlspecialchars($chatbot['colorRespuestaUsuario'] ?? '#219ebc') ; ?>"
                                        oninput="actualizarColores()">
                                    <div id="muestraColorRespuestaUsuario" class="color-circle"></div><br>
                                </div>
                            </div>
                            <div class="nombre-colorc">
                                <label for="colorSecundario">Color Secundario</label><br>
                                <div class="color-selector">
                                    <input type="color" id="colorSecundario" value="<?php echo htmlspecialchars($chatbot['colorSecundario'] ?? '#b6b6b6') ; ?>"
                                        oninput="actualizarColores()">
                                    <div id="muestraColorSecundario" class="color-circle"></div><br>
                                </div>

                                <label for="colorTexto">Color de Texto</label><br>
                                <div class="color-selector">
                                    <input type="color" id="colorTexto" value="<?php echo htmlspecialchars($chatbot['colorTexto'] ?? '#000000' ) ; ?>" oninput="actualizarColores()">
                                    <div id="muestraColorTexto" class="color-circle"></div><br>
    
                                </div>
                                
                            </div>
                        
                                
                        

                            <!-- Contenedor para cargar la imagen con icono adjunto -->
                            <div class="container-archivo">
                                <label for="urlLogotipo">Ingresa la URL del Logotipo</label><br>
                                <div class="select-archivo">
                                    <input type="text" id="urlLogotipo" placeholder="https://tusitio.com/logo.png (.png, .jpg, .svg)" class="input-url" value="<?php echo htmlspecialchars($chatbot['urlLogotipo'] ?? ''); ?>">
                                    <!--<button type="button" onclick="previsualizarImagen()" class="update-logo-button">Actualizar logo</button>--->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="chatbot-principal">
                        <div class="chatbot-container">
                            <div class="chatbot-header" id="chatbot-header">
                                <?php
                                $logo = (!empty($chatbot['urlLogotipo'])) ? $chatbot['urlLogotipo'] : 'img/Logo_cabeza.svg';
                                ?>
                                <img src="<?php echo htmlspecialchars($logo); ?>" alt="Chatbot" class="chatbot-icon" id="chatbotIcon">
                                <p class="txt-titulo-chat" id="txt-titulo-chat"><?php echo htmlspecialchars($chatbot['inp_nombre'] ?? 'IXAH' ) ;?></p>
                                <div class="container1">
                                    <div class="chatbot-min" title="Minimizar" onclick="toggleChatbot()">
                                        <svg class="icono-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M 6 12 C 6 11.449219 6.449219 11 7 11 L 17 11 C 17.550781 11 18 11.449219 18 12 C 18 12.550781 17.550781 13 17 13 L 7 13 C 6.449219 13 6 12.550781 6 12 Z"/>
                                    </svg>

                                    </div>
                                    <div class="chatbot-close" title="Cerrar" onclick="cerrar()">
                                         <svg class="icono-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="miter">
                                            <path d="M 16 8 L 8 16 M 8 8 L 16 16"/>
                                            </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="chatbot-content">
                                <p class="txt-chatbot">
                                    <?php 
                                    $saludo = !empty($chatbot['inp_saludo']) 
                                        ? $chatbot['inp_saludo'] 
                                        : '¡Hola! Soy IXAH, tu asistente virtual en el mundo laboral. ¿En qué te puedo ayudar hoy?';
                                    echo htmlspecialchars($saludo);
                                    ?>
                                </p>
                                <div class="chatbot-buttons">
                                    <button class="chatbot-button">Buscar vacantes por categoría</button>
                                    <button class="chatbot-button">Buscar vacantes por ubicación</button>
                                    <button class="chatbot-button">Seguimiento de mi postulación</button>
                                </div>
                            </div>

                            <div class="user-message2">
                                    <p>vurzolakku@gufum.com</p>
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
<!--Modal de integración -->
    <?php require_once 'modalIntegracion.php'; ?>


    <!-- jQuery y Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
            <script>
        const id_chatbot = <?php echo json_encode($id_chatbot); ?>;
        if (id_chatbot) {
            localStorage.setItem("id_chatbot", id_chatbot);
        }
        </script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/estilo.js"></script>
    <script src="js/custom.js"></script>
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
    <script src="js/guardar.js"></script>
    <script src="js/menuLateral.js" type="module"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Tour Ixah plus-->
    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
    <script src="temp_tour_base/tour_ixah.js"></script>
    <?php include 'guiaFlotante.php'; ?>

</body>

</html>