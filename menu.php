<!-- Función que permite que la validación del usuario sea correcta y 
si no esta validado es redirigido a la página de inicio -->

<!-- Las demás secciones cuentán con esta función con el fin de proteger la información de la empresa -->

<?php

session_start();

if (!isset($_SESSION['idEmpresa'])) {
  session_destroy();
  header("location: ./index.php?error=2");
  exit;
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
  <link rel="shortcut icon" href="logoPagina.png" />
</head>

<body>
  <div class="rectangulo-container">
    <img
      src="img/newLogo.svg"
      width="70px"
      alt="Logo"
      class="img-logo-chiq" />
  </div>
  <header>
    <nav class="navbar">
      <ul class="filas">
        <li><a href="menu.php" class="txt-home">Home</a></li>
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
            <strong>Karla Durán</strong><br />
            <small><?php echo ($_SESSION['idEmpresa']) ?></small>
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
          <!-- Enlace que vincula al botón de cerrar sesión con su respectiva función -->
          <a href="cerrarSesion.php">Cerrar Sesión</a>
        </div>
      </div>

    </div>
  </header>

  <main>
    <div class="container-prin">
      <div class="container-bienv">
        <img src="img/icons8.png" width="70px" alt="Welcome Icon" class="img-Bien" ; />
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

        <div class="container-chats">
          <div class="nombre-chat">
            <p class="txt-chat-edit" id="txt-chat-edit">
              ChatBot para vacantes
            </p>
          </div>
          <div class="container-btn-edit-chat">
            <a href="estilo.php">
              <button class="btn-edit-chat">
                <img src="img/icons9.png" width="35" alt="Edit ChatBot" />
              </button>
            </a>
          </div>
        </div>
      </div>

      <div class="container-agregar">
        <a class="btn-add-chat" href="plan.php">
          <img src="img/add.png" width="60" alt="Add ChatBot" />
        </a>
      </div>
    </div>
  </main>

  <!-- jQuery y Bootstrap JavaScript -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/loginError.js" type="module"></script>
  <script src="js/menuLateral.js" type="module"></script>

</body>

</html>