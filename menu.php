<!-- Función que permite que la validación del usuario sea correcta y 
si no esta validado es redirigido a la página de inicio -->

<!-- Las demás secciones cuentán con esta función con el fin de proteger la información de la empresa -->

<?php

session_start();

if (!isset($_SESSION['id_adm'])) {
  session_destroy();
  header("location: ./index.php?error=2");
  exit;
}

include 'modelo/conexion_bd.php';

$id_adm = $_SESSION['id_adm'];

// Obtener plan actual de la empresa asociada al administrador
$sqlPlan = "SELECT s.nombre_susc, h.estado, h.fecha_contratacion
            FROM historial h
            INNER JOIN suscripcion s ON h.Suscripcion_id_susc = s.id_susc
            INNER JOIN empresa e ON h.Empresa_id_emp = e.id_emp
            WHERE e.id_emp = (SELECT empresa_id_emp FROM administrador WHERE id_adm = ?)
              AND h.estado IN ('activo', 'pendiente_cancelacion')
            ORDER BY h.fecha_contratacion DESC
            LIMIT 1";

$stmtPlan = $conexion->prepare($sqlPlan);
$stmtPlan->bind_param("i", $id_adm);
$stmtPlan->execute();
$resultPlan = $stmtPlan->get_result();

if ($rowPlan = $resultPlan->fetch_assoc()) {
    $planUsuario = $rowPlan['nombre_susc'];
    $estadoSuscripcion = strtolower($rowPlan['estado']);

    // Normalizar estados
    if ($estadoSuscripcion === 'cancelado') $estadoSuscripcion = 'canceled';
    if ($estadoSuscripcion === 'pausado') $estadoSuscripcion = 'paused';
} else {
    $planUsuario = "Sin plan";
    $estadoSuscripcion = "inactivo";
}

$sql = "SELECT c.id_chatbot, c.inp_nombre, t.nombre_tipo_chatbot
        FROM chatbot c
        INNER JOIN tipo_chatbot t ON c.Tipo_Chatbot_idTipo_Chatbot = t.idTipo_Chatbot 
        WHERE c.Administrador_id_adm = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_adm);
$stmt->execute();
$result = $stmt->get_result();

$chatbots = [];
while ($row = $result->fetch_assoc()) {
    $chatbots[] = $row;
}
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
</head>

<body>
  <div class="rectangulo-container">
    <img
      src="img/Logo_cabeza.svg"
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

    <div class="user-dropdown">
      <div class="cont-btn-user" id="close-btn-user">
        <button class="btn-user" id="user-btn">
          <img src="img/user.png" width="30" alt="User Icon" />
        </button>
      </div>
      <!-- Menu lateral -->
      <div class="dropdown-content" id="dropdown-content">
        <div class="d-flex align-items-center px-3 user-info">
          <img src="img/user.png" width="40" alt="User Icon" />
          <div class="div-user">
            <strong><?php echo $_SESSION['nombre_adm'] . ' '. $_SESSION['apellidop_adm']; ?></strong><br />
            <small><?php echo ($_SESSION['correo_adm']) ?></small>
          </div>
        </div>
        <!-- Nuevas leyendas -->
        <!--         Nombre del chatbot (IXAH), versión 1.0.0, "Desarrollado por Giintape Innovahue" Y correo, soporte@giintapeinnovahueteam.onmicrosoft.com -->
        <div class="dropdown-links">
          <div class="user-info">
            <a href="#">Chatbot IXAH</a>
            <span>Versión 1.0.0</span>
          </div>
          <div class="user-info">
            <a href="#">Desarrollado por Giintape Innovahue</a>
            <span> soporte@giintapeinnovahueteam.onmicrosoft.com</span>
          </div>
         
           <div class="user-info">
            <input class="form-check-input <?php echo ($planUsuario === 'free') ? 'grayed-checkbox' : ''; ?>" 
                  type="checkbox" 
                  id="sftpCheckbox" />
            <label class="form-check-label" for="sftpCheckbox">Integración SFTP</label>

            <span><a href="https://billing.stripe.com/p/login/test_00wbIUdPqdvr04O7bObZe00">Actualizar Plan</a></span>

          </div>
          <a class="a1" href="cerrarSesion.php">Cerrar Sesión</a>
        </div>
      </div>
    </div>
  </header>

  <main id="contenidoPrincipal">

    <div class="container-prin">
      <div class="container-bienv">
        
        <p class="txtBien">Bienvenido</p>
        <div class="container-btn-refresh">
          <button class="btn-refresh">
            <img src="img/refresh.png" width="25" alt="Refresh" />
          </button>
        </div>
      </div>

      <div>
        <div class="txt-disena-chat">
          <p>Configuración</p>
        </div>
        <?php if (count($chatbots) > 0): ?>
        <?php foreach ($chatbots as $chatbot): ?>
        <div class="container-chats">
          <div class="nombre-chat">
            <p class="txt-chat-edit" id="txt-chat-edit">
             <?php echo htmlspecialchars($chatbot['inp_nombre']); ?> - Tipo: <small><?php  echo htmlspecialchars ($chatbot['nombre_tipo_chatbot']); ?></small>
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
        <a class="btn-add-chat" href="estilo.php?nuevo=1" >
          <img src="img/add.png" width="60" alt="Add ChatBot" />
        </a>
      </div>
    </div>


  </main>

<div class="modal" id="sftpModal">
  <div class="modal-content">
    <span class="cerrar-modal" id="cerrarIntegracion">&times;</span>
    <h3>Configuración de Integración</h3>
    <form id="formIntegracionSFTP">
      <label>Servidor:</label>
      <input type="text" name="servidor" required />

      <label>Puerto:</label>
      <input type="text" name="puerto" value="22" readonly style="background-color: #eee;" />

      <label>Usuario:</label>
      <input type="text" name="usuario" required />

      <label>Contraseña:</label>
      <input type="password" name="contrasena" required />

      <button class="submit-button-form" type="submit">Guardar</button>
    </form>
  </div>
</div>

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


  <!-- jQuery y Bootstrap JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/loginError.js" type="module"></script>
  <script src="js/menuLateral.js" type="module"></script>
  <script src="js/navegacion.js"></script>
   <script src="js/formularioIntegracion.js"></script>
   
  
   <script>
  window.appData = {
    nombrePlan: '<?php echo $planUsuario; ?>',
    estadoSuscripcion: '<?php echo $estadoSuscripcion; ?>'
  };
</script>
 <script src="js/reactivar_plan.js"></script>

</body>

</html>