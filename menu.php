<!-- Función que permite que la validación del usuario sea correcta y 
si no esta validado es redirigido a la página de inicio -->

<!-- Las demás secciones cuentán con esta función con el fin de proteger la información de la empresa -->
<?php
include 'modelo/consultas_menu.php';

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/sty.css" />
  <link
    href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap"
    rel="stylesheet" />
  <title>Home</title>
  <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    window.IXAH_CONFIG = {
      tieneChatbot: <?= count($chatbots) > 0 ? 'true' : 'false' ?>
    };
  </script>
</head>

<body>
  <div class="rectangulo-container">
    <img
      src="img/LOGOTIPO_IXAH-02.png"
      width="70px"
      alt="Logo"
      class="img-logo-chiq" />
  </div>

  <header>
    <nav class="navbar">
      <ul class="filas">
        <!-- <li><a href="menu.php" class="txt-home">Home</a></li>-->
        <!-- <li><a href="estilo.php">ChatBot para vacantes</a>
                    <ul>
                        <li><a href="#">Chatbot para pedidos</a></li>
                        <li class="divider"></li>
                        <li><a href="#">Crear nuevo Chatbot</a></li>
                        
                    </ul>
                </li> -->
      </ul>
    </nav>
    <?php include 'DatosMenu.php'; ?>

  </header>

  <main id="contenidoPrincipal">

    <div class="container-prin">
      <div class="container-bienv">

        <p class="txtBien">Bienvenido</p>
        <!-- <div class="container-btn-refresh">
         <button class="btn-refresh">
            <img src="img/refresh.png" width="25" alt="Refresh" />
          </button>
        </div>-->
      </div>

      <div>
        <div class="txt-disena-chat">
          <!-- <p>Configuración</p>-->
        </div>
        <?php if (count($chatbots) > 0): ?>
          <?php foreach ($chatbots as $chatbot): ?>
            <div class="container-chats">
              <div class="nombre-chat">
                <p class="txt-chat-edit" id="txt-chat-edit">
                  <?php echo htmlspecialchars($chatbot['inp_nombre']); ?> - Tipo: <small><?php echo htmlspecialchars($chatbot['nombre_tipo_chatbot']); ?></small>
                </p>
              </div>
              <div class="container-btn-edit-chat">
                <a href="estilo.php?id_chatbot=<?php echo $chatbot['id_chatbot']; ?>">
                  <button class="btn-edit-chat">
                    <img src="img/icons9.png" width="35" alt="Edit ChatBot" />
                  </button>
                </a>
              </div>
            </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="text-muted"></p>
  <?php endif; ?>

  <div class="container-agregar">
    <?php if ($totalChatbots < 1): ?>
      <a class="btn-add-chat" href="estilo.php?nuevo=1">
        <img src="img/add.png" width="60" alt="Add ChatBot" />
      </a>
    <?php else: ?>
      <button class="btn-add-chat" disabled style="opacity: 0.5; cursor: not-allowed;">
        <img src="img/add.png" width="60" alt="Add ChatBot" />
      </button>
    <?php endif; ?>
  </div>
    </div>


  </main>

  <!-- Modal Integración SFTP 
  <div class="modal fade" id="sftpModal" tabindex="-1" role="dialog" aria-labelledby="sftpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">

       Header 
        <div class="modal-header">
          <h5 class="modal-title" id="sftpModalLabel">Configuración de Integración</h5>
          <span class="cerrar-modal" id="cerrarIntegracion" data-dismiss="modal">&times;</span>
        </div>

        Body
        <div class="modal-body" style="margin-left: 40px">

          <form id="formIntegracionSFTP">

            <div class="d-flex align-items-center mb-3">
              <input class="form-check-input small-checkbox me-2" type="checkbox" id="sftpCheckbox"
                >
              <label for="sftpCheckbox" class="m-0">Activar integración SFTP</label>
            </div>
            <label>Servidor:</label>
            <input type="text" class="form-control custom-input" name="servidor" required />

            <label>Puerto:</label>
            <input type="text" class="form-control custom-input" name="puerto" value="22" readonly />

            <label>Usuario:</label>
            <input type="text" class="form-control custom-input" name="usuario" required />

            <label>Contraseña:</label>
            <input type="password" class="form-control custom-input" name="contrasena" />


            <label>Ruta de Destino:</label>
            <input type="text" class="form-control custom-input" name="rutaDestino" required />
          </form>
        </div>

       Footer 
        <div class="modal-footer1">
          <button class="submit-button-form" type="submit" form="formIntegracionSFTP">Guardar</button>
        </div>

      </div>
    </div>
  </div>-->


  <div class="modal fade" id="modalAvisoCancelacion" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title">Cancelación en Proceso</h5>
        </div>
        <div class="modal-body">
          <p>Tu suscripción será cancelada al finalizar el periodo actual. Aún puedes usar el sistema hasta esa fecha.</p>
        </div>
        <div class="modal-footer1">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Entendido</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Suscripción Cancelada/Pausada -->
  <div class="modal fade" id="modalSuspension" tabindex="-1" role="dialog" aria-labelledby="modalSuspensionLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalSuspensionLabel">Suscripción inactiva</h5>
        </div>
        <div class="modal-body">
          <p>Tu suscripción ha sido cancelada o pausada. No puedes usar el sistema hasta contratar una nueva suscripción.</p>
          <form id="nuevoPlanForm">
            <div class="form-group">
              <label for="planSelect">Selecciona un plan:</label>
              <select id="planSelect" class="form-control" required>
                <option value="">-- Elige un plan --</option>
                <option value="basico3m">Mensual</option>
                <option value="planAnual">Anual</option>
              </select>
            </div>
            <button type="button" id="btnContratarPlan" class="btn btn-primary mt-2">Contratar Plan</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!--Modal de integración -->
  <?php include 'modalIntegracion.php'; ?>

  <!-- jQuery y Bootstrap JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/loginError.js" type="module"></script>
  <script src="js/menuLateral.js" type="module"></script>
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
  <script src="js/reactivar_plan.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css" />
  <link rel="stylesheet" href="css/tour_ixah.css">
  <script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>
  <script src="js/tour_ixah.js"></script>


</body>

</html>