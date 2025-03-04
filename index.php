<?php
session_start();

if (isset($_SESSION['idEmpresa'])) {
 header("location: menu.php");
};

?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/styles.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap"
      rel="stylesheet"
    />
    <title>Inicio de sesion</title>
    <link rel="shortcut icon" href="img/logoginnatapechiquito.png" />
  </head>

  <body>
    <div class="container-logo">
      <img src="img/Innovahue_logo.svg" class="imgLogo" />
    </div>
    <p class="txtGii">GIINTAPE INNOVAHUE 3</p>

    <div class="vector">
      <img src="img/Vector.png" class="imgVect" />
    </div>

    <!-- Formulario de login -->
    <div class="login-container">
      <form method="POST" action="modelo/login_usuario_bd.php">
        
        <div class="contIn">
          <h1 class="titleIn">Login</h1>
          <div class="contUs">
            <label class="form-label">ID de Empresa</label><br />
            <input
              type="text"
              name="idEmpresa"
              id="empresaId"
              class="txtUsu"
              required
            /><br />

            <label class="form-label">Correo</label><br />

            <input
              type="email"
              name="correo"
              id="email1"
              class="txtMail"
              required
            /><br />
            <label class="form-label">Contraseña</label><br />
            <input
              type="password"
              name="contra"
              id="password"
              class="txtpsw"
              required
            /><br />
            <div class="container-btn-sesion">
              <a href="#" id="link-restablecer" class="link-restablecer"
                >¿Olvidaste la contraseña?</a
              >

              <button
                type="submit"
                name="inicioSesion"
                id="btnSesion"
                class="btnSesion"
              >
                Iniciar sesión
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
    <a href="registerForm.php" id="link-registro" class="link-registro"
                >¿Aun no estás registrado?</a
              >

    <!--Formulario del modal-->
    <div id="reset-password-form" class="modal" style="display: none">
      <div class="modal-content">
        <div class="modal-header">
          <span>Restablecer tu contraseña</span>
          <span class="close" id="close-modal">&times;</span>
        </div>
        <div class="modal-body">
          <p>
            Ingresa tu correo electrónico y te enviaremos una contraseña
            aleatoria para ingresar a su cuenta.
          </p>
          <form id="reset-form">
            <input
              type="email"
              id="email"
              class="txtMail"
              placeholder="Correo electrónico"
              required
            />
            <button type="submit" class="submit-button-form" id="submit-button">
              Enviar contraseña
            </button>
          </form>
        </div>
      </div>
    </div>
    <!--  Alert restablecimiento personalizado -->
    <div id="custom-alert" class="modal" style="display: none">
      <div class="modal-alert">
        <span id="custom-alert-message" class="alert-message"></span>
        <button id="custom-alert-close" class="alert-close">Cerrar</button>
      </div>
    </div>
    <script src="js/restContra.js" type="module"></script>
    <!-- <script src="js/loginError.js" type="module"></script> --> 
    <!-- <p class="txtGii">GIINTAPE INNOVAHUE</p> -->
  </body>
</html>
