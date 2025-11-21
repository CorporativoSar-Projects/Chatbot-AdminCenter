<?php
// panel_admin_ia.php
// Incluye tu validación y consultas previas (como ya haces)
include 'modelo/consultas_menu.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/sty.css" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <title>Panel IA - IXAH</title>
  <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root {
      --primary-color: #2c3e50;
      --secondary-color: #3498db;
      --accent-color: #9b59b6;
      --success-color: #27ae60;
      --warning-color: #f39c12;
      --danger-color: #e74c3c;
      --light-bg: #f8f9fa;
      --card-shadow: 0 8px 25px rgba(0,0,0,0.08);
      --border-radius: 12px;
    }

    body {
      font-family: 'Montserrat', sans-serif;
      background-color: #f5f7fa;
    }

    /* Ajuste del margen entre header y contenido */
    main.container {
      margin-top: 100px !important;
    }

    .panel-card {
      background: #fff;
      border-radius: var(--border-radius);
      box-shadow: var(--card-shadow);
      padding: 24px;
      margin-bottom: 20px;
      border: 1px solid #e9ecef;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .panel-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 30px rgba(0,0,0,0.12);
    }

    .section-title {
      font-weight: 700;
      margin-bottom: 16px;
      color: var(--primary-color);
      font-size: 1.25rem;
      border-bottom: 2px solid var(--secondary-color);
      padding-bottom: 8px;
    }

    .small-muted {
      color: #6c757d;
      font-size: 0.85rem;
    }

    /* Sidebar mejorada */
    .sidebar-nav {
      background: linear-gradient(135deg, var(--primary-color), #34495e);
      border-radius: var(--border-radius);
      padding: 20px;
      color: white;
    }

    .sidebar-nav h5 {
      color: white;
      font-weight: 600;
      margin-bottom: 20px;
      text-align: center;
    }

    .sidebar-nav .nav-link {
      color: rgba(255,255,255,0.9);
      padding: 12px 16px;
      margin: 4px 0;
      border-radius: 8px;
      transition: all 0.3s ease;
      text-decoration: none;
      display: block;
    }

    .sidebar-nav .nav-link:hover {
      background: rgba(255,255,255,0.1);
      color: white;
      transform: translateX(5px);
    }

    /* Botones mejorados */
    .btn-primary {
      background: linear-gradient(135deg, var(--secondary-color), #2980b9);
      border: none;
      border-radius: 8px;
      padding: 10px 20px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(52, 152, 219, 0.4);
    }

    .btn-success {
      background: linear-gradient(135deg, var(--success-color), #229954);
      border: none;
      border-radius: 8px;
      padding: 10px 20px;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-success:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(39, 174, 96, 0.4);
    }

    /* Formularios mejorados */
    .form-control {
      border-radius: 8px;
      border: 1px solid #ddd;
      padding: 12px 15px;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: var(--secondary-color);
      box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
      transform: translateY(-1px);
    }

    /* Lista de prompts mejorada */
    .prompt-item {
      background: var(--light-bg);
      border-radius: 8px;
      padding: 16px;
      margin-bottom: 12px;
      border-left: 4px solid var(--secondary-color);
      transition: all 0.3s ease;
    }

    .prompt-item:hover {
      background: #e8f4fd;
      transform: translateX(5px);
    }

    /* Revisiones mejoradas */
    .revision-item {
      background: #fff;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 16px;
      border: 1px solid #e9ecef;
      border-left: 4px solid var(--accent-color);
      transition: all 0.3s ease;
    }

    .revision-item:hover {
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    /* Badges y estados */
    .badge-function {
      background: var(--secondary-color);
      color: white;
      padding: 4px 8px;
      border-radius: 6px;
      font-size: 0.75rem;
    }

    /* Grid mejorado */
    .form-row {
      margin-bottom: 20px;
    }

    .form-group label {
      font-weight: 600;
      color: var(--primary-color);
      margin-bottom: 8px;
    }

    /* Responsive */
    @media (max-width: 768px) {
      main.container {
        margin-top: 20px !important;
      }
      
      .panel-card {
        padding: 16px;
        margin-bottom: 16px;
      }
      
      .section-title {
        font-size: 1.1rem;
      }
    }

    /* Animaciones suaves */
    .fade-in {
      animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Estado de carga */
    .loading {
      opacity: 0.7;
      pointer-events: none;
    }

    .spinner {
      border: 2px solid #f3f3f3;
      border-top: 2px solid var(--secondary-color);
      border-radius: 50%;
      width: 20px;
      height: 20px;
      animation: spin 1s linear infinite;
      display: inline-block;
      margin-right: 8px;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>

<body>
  <?php // header y menú tal como lo tienes actualmente ?>
  <div class="rectangulo-container">
    <img src="img/Logo_cabeza.svg" width="70px" alt="Logo" class="img-logo-chiq" />
  </div>

  <header>
    <nav class="navbar">
      <ul class="filas">
        <!-- Tu menú actual aquí -->
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
            <strong><?php echo $_SESSION['nombre_adm'] . ' ' . $_SESSION['apellidop_adm']; ?></strong><br />
            <small><?php echo ($_SESSION['correo_adm']) ?></small>
          </div>
        </div>
        <div class="dropdown-links">
          <div class="user-info">
            <a href="#">Chatbot IXAH</a>
            <span>Versión 1.0.0</span>
          </div>
          <div class="user-info">
            <a href="menu.php">Home</a>
          </div>
          <div class="user-info">
            <a href="vacantes.php">Vacantes</a>
          </div>
          <div class="user-info">
            <a href="candidatos.php">Candidatos</a>
          </div>
          <div class="user-info">
            <a href="log_errores.php">Errores de los ChatBots</a>
          </div>
          <div class="user-info">
            <a href="#">Desarrollado por Giintape Innovahue</a>
            <span>Ayuda</span>
          </div>
          <div class="user-info">
            <a href="#" id="sftpLink" data-toggle="modal" data-target="#sftpModal" style="text-decoration: none; color: inherit; display: block; margin-bottom: 10px;">
              Integración SFTP
            </a>
            <a href="https://billing.stripe.com/p/login/fZe3f33cggofeBy144" target="_blank">Actualizar Plan</a>
          </div>
          <a class="a1" href="cerrarSesion.php">Cerrar Sesión</a>
        </div>
      </div>
    </div>
  </header>

  <main class="container mt-5 fade-in">
    <div class="row">
      <!-- SIDEBAR (izquierda) -->
      <div class="col-md-3">
        <div class="sidebar-nav">
          <h5>IXAH - Panel IA</h5>
          <p class="small-muted mb-3" style="color: rgba(255,255,255,0.8);">
            Usuario: <strong><?php echo $_SESSION['nombre_adm']; ?></strong>
          </p>
          <hr style="border-color: rgba(255,255,255,0.2);"/>
          <nav class="nav flex-column">
            <a href="#model-config" class="nav-link">🤖 Modelos IA</a>
            <a href="#prompts" class="nav-link">📝 Prompts</a>
            <a href="#revisiones" class="nav-link">🔍 Revisiones</a>
            <a href="#tokens" class="nav-link">⚡ Tokens</a>
          </nav>
        </div>

        <div class="panel-card text-center">
          <h6 class="mb-2">🔗 Integración SFTP</h6>
          <a href="#" id="sftpLinkPanel" data-toggle="modal" data-target="#sftpModal" class="btn btn-outline-primary btn-sm">
            Configurar SFTP
          </a>
        </div>
      </div>

      <!-- CONTENIDO PRINCIPAL (derecha) -->
      <div class="col-md-9">
        <!-- 1) Model Config -->
        <section id="model-config" class="panel-card">
          <h5 class="section-title">🤖 Configuración de Modelos</h5>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="selectFunction">Función</label>
              <select id="selectFunction" class="form-control">
                <option value="mejorar_descripcion">🎯 Mejorar descripción</option>
                <option value="analisis_cv">📊 Análisis CV</option>
                <option value="chat_general">💬 Chat general</option>
              </select>
            </div>

            <div class="form-group col-md-6">
              <label for="inputModelName">Modelo</label>
              <input id="inputModelName" class="form-control" placeholder="Ej: gpt-4, claude-3, etc." />
            </div>
          </div>

          <div class="mt-3">
            <button id="btnSaveModel" class="btn btn-primary">
              <span class="spinner" style="display: none;"></span>
              Guardar modelo
            </button>
            <span id="modelSaveMsg" class="small-muted ml-2"></span>
          </div>
        </section>

        <!-- 2) Prompts -->
        <section id="prompts" class="panel-card">
          <h5 class="section-title">📝 Gestión de Prompts</h5>

          <form id="formCreatePrompt">
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="promptName">Nombre del Prompt</label>
                <input id="promptName" name="name" class="form-control" required placeholder="Ej: Mejora descripción técnica" />
              </div>

              <div class="form-group col-md-4">
                <label for="promptFunction">Función asociada</label>
                <select id="promptFunction" name="function" class="form-control">
                  <option value="mejorar_descripcion">🎯 Mejorar descripción</option>
                  <option value="analisis_cv">📊 Análisis CV</option>
                  <option value="chat_general">💬 Chat general</option>
                </select>
              </div>

              <div class="form-group col-md-4">
                <label for="btnCreatePrompt">&nbsp;</label>
                <button id="btnCreatePrompt" class="btn btn-success btn-block">
                  <span class="spinner" style="display: none;"></span>
                  Crear plantilla
                </button>
              </div>
            </div>

            <div class="form-group">
              <label for="promptTemplate">Template del Prompt</label>
              <textarea id="promptTemplate" name="template" class="form-control" rows="4" required 
                        placeholder="Usa placeholders como {puesto}, {candidato}, {empresa}..."></textarea>
              <small class="form-text text-muted">
                Variables disponibles: {puesto}, {candidato}, {empresa}, {habilidades}, {experiencia}
              </small>
            </div>
          </form>

          <hr/>
          <h6 class="mb-3">📋 Plantillas existentes</h6>
          <div id="promptList" class="fade-in">
            <div class="text-center text-muted py-4">
              <div class="spinner mx-auto"></div>
              <p class="mt-2">Cargando plantillas...</p>
            </div>
          </div>
        </section>

        <!-- 3) Revisiones -->
        <section id="revisiones" class="panel-card">
          <h5 class="section-title">🔍 Revisiones Generadas</h5>
          <div class="mb-3">
            <button id="btnRefreshRevisions" class="btn btn-outline-primary btn-sm">
              <span class="spinner" style="display: none;"></span>
              🔄 Actualizar lista
            </button>
          </div>
          <div id="revisionsList" class="fade-in">
            <div class="text-center text-muted py-4">
              <div class="spinner mx-auto"></div>
              <p class="mt-2">Cargando revisiones...</p>
            </div>
          </div>
        </section>

        <!-- 4) Tokens -->
        <section id="tokens" class="panel-card">
          <h5 class="section-title">⚡ Estado de Tokens</h5>
          <div id="tokensInfo" class="fade-in">
            <div class="text-center text-muted py-4">
              <div class="spinner mx-auto"></div>
              <p class="mt-2">Cargando información de tokens...</p>
            </div>
          </div>
        </section>

      </div> <!-- end col-md-9 -->
    </div>
  </main>

  <!-- SCRIPTS -->
  <script type="module" src="js/IA/panel-admin-ia.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>