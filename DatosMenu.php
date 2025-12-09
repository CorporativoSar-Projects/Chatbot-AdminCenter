<?php
include 'modelo/consultas_menu.php';
?>

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
        <strong><?php echo $_SESSION['nombre_adm'] . ' ' . $_SESSION['apellidop_adm']; ?></strong><br />
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
        <a href="#" style="text-decoration: none; color: inherit; display: block; margin-bottom: 10px;">Desarrollado por Giintape Innovahue</a>
        <a href="https://giintapeinnovahue.freshdesk.com/support/tickets/new" target="_blank">Soporte técnico</a>
      </div>
    </div>

    <div class="user-info">
      <a href="modalIntegracion.php" id="sftpLink" data-toggle="modal" data-target="#sftpModal" style="text-decoration: none; color: inherit; display: block; margin-bottom: 10px;">
        Integraciones
      </a>

      <a href="https://billing.stripe.com/p/login/fZe3f33cggofeBy144" target="_blank">Actualizar Plan</a>

    </div>
    <a class="a1" href="cerrarSesion.php">Cerrar Sesión</a>
  </div>
</div>
</div>