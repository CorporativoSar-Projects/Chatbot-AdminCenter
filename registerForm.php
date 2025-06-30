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
  <link rel="shortcut icon" href="img/logoginnatapechiquito.png" />
</head>

<body>
  <div class="container-logo">
    <img src="img/newLogo.svg" class="imgLogo" />
  </div>


  <div class="vector">
    <img src="img/Vector.png" class="imgVect" />
  </div>

  <!-- Formulario registro -->
  <div class="container-form">
  <div class="registro-container">
    <h2>Registrate</h2>
  <div class="progressbar">
  <div class="progress-step active" data-step="0">
    <div class="icon"><i class="fas fa-building"></i></div>
  <div class="label">Empresa</div>
    </div>
    <div class="progress-step" data-step="1">
      <div class="icon"><i class="fas fa-user"></i></div>
      <div class="label">Administrador</div>
    </div>
  </div>

    <form id="multi-step-form" method="POST" action="modelo/login_registro_bd.php">
      <div class="form-step active">
       <div class="form-group">
          <input
            type="text" name="RFC_emp" placeholder="RFC de la empresa"
            required>
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
            type="text" name="ubicacion_emp" placeholder="Ubicación"
            required>
      </div>
      <div class="form-group">
          <input
            type="text" name="url_cs_emp" placeholder="URL de Funcionamiento"
            required>
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
            <button type="button" class="btnPrev" id="btnPrev">Anterior</button>
            <button type="submit" name="Registro"  id="btnRegistro" class="btnRegistro"> Regístrate </button>
          </div>
          <a href="index.php" id="link-miembro" class="link-miembro">¿Ya eres miembro? Inicia sesión</a>
        </div>
    </form>
  </div>


<script src="js/steps.js"></script>


  <!-- <p class="txtGii">GIINTAPE INNOVAHUE</p> -->
</body>

</html>