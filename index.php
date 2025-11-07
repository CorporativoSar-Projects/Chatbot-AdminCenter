<!-- Función que redirige a la página principal si el usuario ingresa los datos correctos -->

<?php
session_start();

if (isset($_SESSION['id_adm'])) {
  header("location: menu.php");
  exit;
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
    rel="stylesheet" />
  <title>Inicio de sesion</title>
  <link rel="shortcut icon" href="img/Logo_cabeza.svg" />

</head>
<!-- Agregue una clase a este body para modificar la altura 
y no altere el comportamiento de las demás secciones -->

<body>
  <!-- habia agregado este div para intentar modificar la altura de la página
  y agregar algunas propiedades respecto la altura pero tampoco funciono -->
  <!-- <div class="page"> -->
  <div class="container-logo">
     <div class="bloque-escritorio">
    <img src="img/LOGO_IXAH.svg" class="imgLogo escritorio" />
    <p class="texto-escritorio">Conectando el mejor talento,<br>
     con las mejores oportunidades</p>

     </div>
  <img src="img/logo_cabeza.svg" class="imgLogo movil" alt="Logo móvil">


  </div>
  <!-- Texto anterior -->
  <!-- <p class="txtGii">GIINTAPE INNOVAHUE 3</p> -->

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
            name="id_emp"
            id="empresaId"
            class="txtUsu"
            required /><br />

          <label class="form-label">Correo</label><br />
          <input
            type="email"
            name="correo_adm"
            id="email1"
            class="txtMail"
            required /><br />
          <label class="form-label">Contraseña</label><br />
          <input
            type="password"
            name="pass_adm"
            id="password"
            class="txtpsw"
            required /><br />
          <div class="container-btn-sesion">
            <!-- Enlace para restablecer la contraseña -->
            <a href="#" id="link-restablecer" class="link-restablecer">¿Olvidaste la contraseña?</a>
            <a href="registerForm.php" id="link-registro" class="link-registro">¿Aún no estás registrado?</a>


            <button
              type="submit"
              name="inicioSesion"
              id="btnSesion"
              class="btnSesion">
              Iniciar sesión
            </button>
            <!-- Enlace para registrarse -->

          </div>
        </div>
      </div>
    </form>
  </div>


  <!--Formulario del modal para el restablecimiento de la contraseña-->
  <div id="reset-password-form" class="modal-rest" style="display: none">
    <div class="modal-content-rest">
      <div class="modal-header-rest">
        <span>Restablecer tu contraseña</span>
        <span class="close" id="close-modal">&times;</span>
      </div>
      <div class="modal-body-rest">
        <p>
          Ingresa tu correo electrónico y te enviaremos una contraseña
          aleatoria para ingresar a su cuenta.
        </p>
        <form id="reset-form">
          <input
            type="email"
            id="email"
            class="txtMail-rest"
            placeholder="Correo electrónico"
            required />
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
      <button id="custom-alert-close" type="button" class="alert-close">Cerrar</button>
    </div>
  </div>
  <!-- Se vincula el archivo js que contiene el restablecimiento de la contraseña -->
  <script src="js/restContra.js" type="module"></script>
  <!-- Se vincula el archivo js que contiene el mensaje de error del login -->
  <script src="js/loginError.js" type="module"></script>
  <!-- <p class="txtGii">GIINTAPE INNOVAHUE</p> -->

</body>

</html>