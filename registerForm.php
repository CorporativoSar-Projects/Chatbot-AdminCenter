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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" >
  
  <title>Registro de usuario</title>
  <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
</head>

<body>
  <div class="container-logo">
    <div class="bloque-escritorio">
    <img src="img/LOGO_IXAH.svg" class="imgLogo escritorio"/>
      <p class="texto-escritorio">Conectando el mejor talento,<br>
     con las mejores oportunidades</p>

     </div>
  <img src="img/logo_cabeza.svg" class="imgLogo movil" alt="Logo móvil">
  </div>


  <div class="vector">
    <img src="img/Vector.png" class="imgVect" />
  </div>

  <!-- Formulario registro -->
  <div class="container-form">
  <div class="registro-container">
    <h1 class="titleRe">Registrate</h1>
  <div class="progressbar">
  <div class="progress-step active" data-step="0">
    <div class="icon"><i class="fas fa-building"></i></div>
  <div class="label">Empresa</div>
    </div>
    <div class="progress-step" data-step="1">
      <div class="icon"><i class="fas fa-user"></i></div>
      <div class="label">Administrador</div>
    </div>
     <div class="progress-step" data-step="2">
    <div class="icon"><i class="fas fa-credit-card"></i></div>
    <div class="label">Pago</div>
  </div>
  </div>
 

    <form id="multi-step-form" method="POST" action="modelo/login_registro_bd.php">
      <div class="form-step active">
       <div class="form-group">
          <input
            type="text" name="RFC_emp" placeholder="RFC de la empresa">
       </div>
        <div class="form-group">
          <input
            type="text" name="nombre_emp" placeholder="Nombre de la empresa"
            required>
      </div>
      <div class="form-group">
          <input
            type="text" name="sitioweb_emp" placeholder="Sitio web"
            required>
      </div>
      <div class="form-group">
          <input
            type="text" name="codigoPostal_emp"  maxlength="5" placeholder="Código Postal"
            required>
      </div>
      <div class="form-group">
          <select
            type="text" name="estado_emp" 
            required>

              <option value="">Selecciona un estado</option>
              <option value="MX-AGU">Aguascalientes</option>
              <option value="MX-BCN">Baja California</option>
              <option value="MX-BCS">Baja California Sur</option>
              <option value="MX-CAM">Campeche</option>
              <option value="MX-CHP">Chiapas</option>
              <option value="MX-CHH">Chihuahua</option>
              <option value="MX-CMX">Ciudad de México</option>
              <option value="MX-COA">Coahuila</option>
              <option value="MX-COL">Colima</option>
              <option value="MX-DUR">Durango</option>
              <option value="MX-GUA">Guanajuato</option>
              <option value="MX-GRO">Guerrero</option>
              <option value="MX-HID">Hidalgo</option>
              <option value="MX-JAL">Jalisco</option>
              <option value="MX-MEX">Estado de México</option>
              <option value="MX-MIC">Michoacán</option>
              <option value="MX-MOR">Morelos</option>
              <option value="MX-NAY">Nayarit</option>
              <option value="MX-NLE">Nuevo León</option>
              <option value="MX-OAX">Oaxaca</option>
              <option value="MX-PUE">Puebla</option>
              <option value="MX-QUE">Querétaro</option>
              <option value="MX-ROO">Quintana Roo</option>
              <option value="MX-SLP">San Luis Potosí</option>
              <option value="MX-SIN">Sinaloa</option>
              <option value="MX-SON">Sonora</option>
              <option value="MX-TAB">Tabasco</option>
              <option value="MX-TAM">Tamaulipas</option>
              <option value="MX-TLA">Tlaxcala</option>
              <option value="MX-VER">Veracruz</option>
              <option value="MX-YUC">Yucatán</option>
              <option value="MX-ZAC">Zacatecas</option>
            </select>
      </div>
      <div class="form-group">
          <input
            type="text" name="url_cs_emp" placeholder="URL de Funcionamiento">
      </div>
      <div class="buttons">
        <button type="button" class="btnNext" id="btnNext">Siguiente</button>
      </div>
    </div>

    <!-- Paso 2 -->
    <div class="form-step">
      <div class="form-group">
        <input type="email" name="correo_adm"
            id="email1" placeholder="Correo electrónico" required>
      </div>
      <div class="form-group">
        <input type="text" name="nombre_adm"  placeholder="Nombre(s)" required>
      </div>
      <div class="form-group">
        <input type="text" name="apellidop_adm"   placeholder="Apellido paterno" required>
      </div>
      <div class="form-group">
        <input type="text" name="apellidom_adm"   placeholder="Apellido materno" required>
      </div>
      <div class="form-group">
        <input type="tel" name="tel_adm" placeholder="Teléfono" required>
      </div>
      <div class="form-group">
        <input type="password" name="pass_adm"  id="password"
           placeholder="Contraseña" required>
      </div>
      <div class="buttons">
      <button type="button" class="btnPrev">Anterior</button>
      <button type="button" class="btnNext">Siguiente</button>
      </div>
        </div>

  <div class="form-step">
  <div class="form-group">
        <label for="tipo_suscripcion">Tipo de suscripción</label>
        <select name="nombre_susc" id="tipo_suscripcion" required>
          <option value="">Selecciona una opción</option>
          <option value="free">Plan Free</option>
          <option value="basicoMensual">Plan Básico - Mensual </option>
          <option value="basicoAnual">Plan Básico - Anual</option>
          <!--  <option value="prueba">prueba de un dia</option>-->
        </select>
    </div>

     <!-- reCAPTCHA -->
  <div class="form-group">
    <div class="g-recaptcha" data-sitekey="6Lcv0fwrAAAAAH92a-JuO3cmDsMOoSebu9amnXXf"></div>
  </div>

          <div class="buttons">
            <button type="button" class="btnPrev" id="btnPrev">Anterior</button>
            <button type="button" name="Registro"  id="btnRegistro" class="btnRegistro"> Regístrate y Paga </button>
          </div>
          <a href="index.php" id="link-miembro" class="link-miembro">¿Ya eres miembro? Inicia sesión</a>
        </div>
    </form>
  </div>


<script src="js/steps.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script src="https://js.stripe.com/v3/"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/pago.js"></script>

  <!-- <p class="txtGii">GIINTAPE INNOVAHUE</p> -->
</body>

</html>