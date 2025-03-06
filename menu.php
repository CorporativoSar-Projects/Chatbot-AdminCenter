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
      rel="stylesheet"
    />
    <title>Home</title>
    <link rel="shortcut icon" href="logoPagina.png" />
  </head>

  <body>
    <div class="rectangulo-container">
      <img
        src="img/logochiquito.png"
        width="70px"
        alt="Logo"
        class="img-logo-chiq"
      />
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
        <div class="cont-btn-user">
          <button class="btn-user">
            <img src="img/user.png" width="30" alt="User Icon" />
          </button>
        </div>

        <div class="dropdown-content">
          <div class="d-flex align-items-center px-3 user-info">
            <img src="img/user.png" width="40" alt="User Icon" />
            <div class="div-user">
              <strong>Karla Durán</strong><br />
              <small>kduranc@giint...</small>
            </div>
          </div>

          <div class="dropdown-links">
            <div class="user-info">
              <a href="#">ChatBot para vacantes</a>
              <span>Plan Básico Mensual</span>
            </div>
            <div class="user-info">
              <a href="#">ChatBot para pedidos</a>
              <span>Plan Básico 3 Meses</span>
            </div>

            <a href="cerrarSesion.php">Cerrar Sesión</a>
          </div>
        </div>
      </div>
    </header>

    <main>
      <div class="container-prin">
        <div class="container-bienv">
          <img src="img/icons8.png" width="70px" alt="Welcome Icon" />
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
  </body>
</html>
