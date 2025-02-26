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
    <title>Registro de usuario</title>
    <link rel="shortcut icon" href="img/logoginnatapechiquito.png" />
  </head>

  <body>
    <div class="container-logo">
      <img src="img/Innovahue_logo.svg" class="imgLogo" />
    </div>
    <p class="txtGii">GIINTAPE INNOVAHUE</p>

    <div class="vector">
      <img src="img/Vector.png" class="imgVect" />
    </div>

    <div class="registro-container">
      <form id=register-form>
        <div class="contIn">
          <h1 class="titleIn">Regístrate</h1>
          <div class="contUs">
            <label class="form-label">ID de Empresa</label><br />
            <input
              type="text"
              name="idEmpresa"
              id="empresaId2"
              class="txtUsu"
              required
            /><br />

            <label class="form-label">Correo</label><br />

            <input
              type="email"
              name="correo"
              id="email2"
              class="txtMail"
              
              required
            /><br />
            <label class="form-label">Contraseña</label><br />
            <input
              type="password"
              name="contra"
              id="password2"
              class="txtpsw"
              required
            /><br />
            <div class="container-btn-registro">
              <a href="index.php" id="link-miembro" class="link-miembro"
                >¿Ya eres miembro?Inicia sesión aquí</a
              >
              <button
                type="submit"
                name="Registro"
                id="btnRegistro"
                class="btnRegistro"
              >
                Regístrate
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!--  Alert registro personalizado -->
    
    <div id="custom-alert" class="modal" style="display: none">
      <div class="modal-alert">
        <span id="custom-alert-message-register" class="alert-message"></span>
        <button id="custom-alert-close" class="alert-close">Cerrar</button>
      </div>
    </div>
    <script src="js/registerForm.js"></script>
    
    
    
    <!-- <p class="txtGii">GIINTAPE INNOVAHUE</p> -->
  </body>
</html>
