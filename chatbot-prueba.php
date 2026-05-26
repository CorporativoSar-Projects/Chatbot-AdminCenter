<?php
include 'modelo/consultas_menu.php';

// Si la sesión no está iniciada, entonces iníciala
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_adm'])) {
    header("location: ./index.php?error=2");
    exit;
}

// Conexión a la base de datos
include 'modelo/conexion_bd.php';

// Consulta para obtener las vacantes con conteo de candidatos
$query = "
    SELECT 
        p.id_vacante,
               p.link_vacante,
        e.nombre_emp as empresa,
        COUNT(p.Candidato_id_candidate) as total_candidatos,
        MAX(p.fecha) as fecha_ultima_postulacion,
        GROUP_CONCAT(DISTINCT c.nombre_candidate, ' ', c.apellidop_candidate SEPARATOR ', ') as nombres_candidatos
    FROM postulaciones p
    INNER JOIN empresa e ON p.Empresa_id_emp = e.id_emp
    INNER JOIN candidato c ON p.Candidato_id_candidate = c.id_candidate
    GROUP BY p.id_vacante, p.id_vacante_csv, p.link_vacante, e.nombre_emp
    ORDER BY fecha_ultima_postulacion DESC
";

$result = mysqli_query($conexion, $query);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

$vacantes = [];
while ($row = mysqli_fetch_assoc($result)) {
    $vacantes[] = $row;
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
  <title>Vacantes - Admin</title>
  <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css"/>

  <style>
    #main-container {
        display: flex;
        height: calc(100vh - 80px);
        margin-top: 80px;
        transition: all 0.3s ease;
    }

    #content {
        width: 75%;
        padding: 30px;
        overflow-y: auto;
        transition: width 0.3s ease;
        background-color: #ffffff;
    }

    .page-header {
      background: linear-gradient(135deg, #002B45 0%, #3ca6e5 100%);
      color: white;
      padding: 25px 30px;
      border-radius: 15px;
      margin-bottom: 30px;
      box-shadow: 0 8px 25px rgba(0, 43, 69, 0.15);
    }
    
    .page-header h2 {
      margin: 0;
      font-weight: 700;
      font-size: 28px;
    }

    .table-container {
      background: white;
      border-radius: 15px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      margin-bottom: 30px;
    }

    .table thead th {
      background: linear-gradient(135deg, #002B45 0%, #3ca6e5 100%);
      color: white;
      border: none;
      padding: 15px 20px;
      font-weight: 600;
      font-size: 14px;
    }
    
    .table tbody tr {
      transition: all 0.3s ease;
    }
    
    .table tbody tr:hover {
      background-color: rgba(60, 166, 229, 0.05);
      transform: translateY(-1px);
    }
    
    .table tbody td {
      padding: 15px 20px;
      border-color: #e9ecef;
      vertical-align: middle;
    }

    .btn-candidates {
      background: linear-gradient(135deg, #002B45 0%, #3ca6e5 100%);
      color: white;
      border: none;
      border-radius: 25px;
      padding: 8px 20px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(0, 43, 69, 0.2);
    }
    
    .btn-candidates:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 43, 69, 0.3);
      color: white;
      text-decoration: none;
    }

    .btn-link {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white;
      border: none;
      border-radius: 20px;
      padding: 6px 15px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
      text-decoration: none;
      display: inline-block;
      margin-right: 8px;
    }
    
    .btn-link:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
      color: white;
      text-decoration: none;
    }

    .badge-count {
      background: linear-gradient(135deg, #FF6B6B 0%, #FF8E8E 100%);
      color: white;
      border-radius: 50%;
      width: 25px;
      height: 25px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: bold;
      margin-left: 5px;
    }

    .vacante-id {
      background: #f8f9fa;
      padding: 5px 10px;
      border-radius: 8px;
      font-weight: bold;
      color: #002B45;
      font-family: monospace;
    }

    .empresa-badge {
      background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
      color: white;
      padding: 4px 12px;
      border-radius: 15px;
      font-size: 12px;
      font-weight: 600;
    }

    .fecha-vacante {
      font-size: 12px;
      color: #6c757d;
    }

    .candidatos-list {
      font-size: 12px;
      color: #6c757d;
      max-width: 200px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    /* Estilos del Chat */
    #chat-panel {
        width: 25%;
        background-color: #f3f3f3;
        border-left: 1px solid #c5c5c5;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
        box-shadow: -4px 0px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    #chat-header {
        background: linear-gradient(90deg, rgba(0, 43, 69) 27%, rgba(60, 166, 229) 100%);
        color: white;
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: "Montserrat", sans-serif;
        font-weight: bold;
    }

    #chat-header button {
        background: none;
        border: none;
        cursor: pointer;
        color: white;
    }

    #chat-body {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        background-color: #ffffff;
        scrollbar-width: thin;
        scrollbar-color: #3ca6e5 #f1f1f1;
    }

    .chat-message {
        margin-bottom: 15px;
        padding: 12px 16px;
        border-radius: 20px;
        max-width: 85%;
        font-family: "Montserrat", sans-serif;
        font-size: 14px;
        line-height: 1.4;
        box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .user-message {
        background: linear-gradient(90deg, rgba(0, 43, 69) 27%, rgba(60, 166, 229) 100%);
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 5px;
    }

    .bot-message {
        background-color: #e2e3e5;
        color: #002B45;
        align-self: flex-start;
        border-bottom-left-radius: 5px;
    }

    #chat-footer {
        padding: 15px;
        border-top: 1px solid #c5c5c5;
        display: flex;
        background-color: #f8f9fa;
        gap: 10px;
    }

    #chat-footer input {
        flex: 1;
        padding: 12px 16px;
        border-radius: 25px;
        border: 1px solid #c5c5c5;
        font-family: "Montserrat", sans-serif;
        font-size: 14px;
        outline: none;
    }

    #chat-footer button {
        padding: 12px 20px;
        border: none;
        border-radius: 25px;
        background: linear-gradient(90deg, rgba(0, 43, 69) 27%, rgba(60, 166, 229) 100%);
        color: white;
        cursor: pointer;
        font-family: "Montserrat", sans-serif;
        font-weight: bold;
    }

    #chat-panel.hidden {
        width: 0;
        overflow: hidden;
        border-left: none;
    }

    #content.fullwidth {
        width: 100%;
    }

    #chat-open-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 70px;
        height: 70px;
        background: linear-gradient(90deg, rgba(0, 43, 69) 27%, rgba(60, 166, 229) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        z-index: 9999;
        border: none;
    }

    #chat-open-btn img {
        width: 35px;
        filter: brightness(0) invert(1);
    }

    #chat-body::-webkit-scrollbar {
        width: 6px;
    }

    #chat-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #chat-body::-webkit-scrollbar-thumb {
        background: linear-gradient(90deg, rgba(0, 43, 69) 27%, rgba(60, 166, 229) 100%);
        border-radius: 10px;
    }

    #chat-body::-webkit-scrollbar-thumb:hover {
        background: #002B45;
    }
  </style>
</head>

<body>
  <div id="chat-open-btn" style="display:none;">
      <img src="https://img.icons8.com/ios-filled/24/ffffff/chat.png" alt="Abrir Chat">
  </div>

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
      </ul>
    </nav>

    <div class="user-dropdown">
      <div class="cont-btn-user" id="close-btn-user">
        <button class="btn-user" id="user-btn">
          <img src="img/user.png" width="30" alt="User Icon" />
        </button>
      </div>
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
          <a href="https://billing.stripe.com/p/login/fZe3f33cggofeBy144" target="_blank">Actualizar Plan</a>
          <a class="a1" href="cerrarSesion.php">Cerrar Sesión</a>
        </div>
      </div>
    </div>
  </header>

  <div id="main-container">
    <div id="content">
        <!-- Header de la página -->
        <div class="page-header">
            <h2>📊 Vacantes Activas</h2>
        </div>

        <!-- Contenedor de la tabla -->
        <div class="table-container">
            <table id="tablaVacantes" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID Vacante</th>
                        <th>Empresa</th>
                        <th>Candidatos</th>
                        <th>Última Postulación</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($vacantes) > 0): ?>
                        <?php foreach ($vacantes as $index => $vacante): ?>
                        <tr>
                            <td>
                                <div class="vacante-id"><?php echo htmlspecialchars($vacante['id_vacante']); ?></div>
                                <?php if (!empty($vacante['id_vacante_csv'])): ?>
                                    <small class="text-muted">CSV: <?php echo htmlspecialchars($vacante['id_vacante_csv']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="empresa-badge"><?php echo htmlspecialchars($vacante['empresa']); ?></span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold"><?php echo $vacante['total_candidatos']; ?> candidatos</span>
                                    <?php if (!empty($vacante['nombres_candidatos'])): ?>
                                        <div class="candidatos-list ms-2" title="<?php echo htmlspecialchars($vacante['nombres_candidatos']); ?>">
                                            <?php echo htmlspecialchars($vacante['nombres_candidatos']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="fecha-vacante">
                                <?php echo date('d/m/Y H:i', strtotime($vacante['fecha_ultima_postulacion'])); ?>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php if (!empty($vacante['link_vacante'])): ?>
                                        <a href="<?php echo htmlspecialchars($vacante['link_vacante']); ?>" target="_blank" class="btn-link">
                                            🔗 Enlace
                                        </a>
                                    <?php endif; ?>
                                    <a href="candidatos.php?id_vacante=<?php echo urlencode($vacante['id_vacante']); ?>" class="btn btn-candidates">
                                        👥 Ver Candidatos <span class="badge-count"><?php echo $vacante['total_candidatos']; ?></span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <h5>No hay vacantes registradas</h5>
                                    <p>Las vacantes aparecerán aquí cuando los candidatos se postulen.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="chat-panel">
        <div id="chat-header">
            <span>Chatbot</span>
            <button id="toggle-chat">
                <img id="toggle-icon" src="https://img.icons8.com/ios-filled/24/ffffff/chevron-left.png" alt="Toggle">
            </button>
        </div>
        <div id="chat-body"></div>
        <div id="chat-footer">
            <input type="text" id="chat-input" placeholder="Escribe tu mensaje...">
            <button id="send-btn">Enviar</button>
        </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

  <script>
    $(document).ready(function() {
        // Inicializar DataTable
        $('#tablaVacantes').DataTable({
            paging: true,
            pageLength: 10,
            lengthChange: true,
            searching: true,
            ordering: true,
            order: [[3, 'desc']], // Ordenar por fecha descendente
            responsive: true,
            language: {
                "search": "Buscar:",
                "lengthMenu": "Mostrar _MENU_ registros por página",
                "zeroRecords": "No se encontraron registros",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "No hay registros disponibles",
                "infoFiltered": "(filtrado de _MAX_ registros totales)",
                "paginate": {
                    "first": "Primera",
                    "last": "Última",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            }
        });

        // Funcionalidad del Chat
        const toggleBtn = document.getElementById('toggle-chat');
        const chatPanel = document.getElementById('chat-panel');
        const content = document.getElementById('content');
        const chatBody = document.getElementById('chat-body');
        const input = document.getElementById('chat-input');
        const sendBtn = document.getElementById('send-btn');
        const chatOpenBtn = document.getElementById('chat-open-btn');

        toggleBtn.addEventListener('click', () => {
            chatPanel.classList.add('hidden');
            content.classList.add('fullwidth');
            chatOpenBtn.style.display = 'flex';
        });

        chatOpenBtn.addEventListener('click', () => {
            chatPanel.classList.remove('hidden');
            content.classList.remove('fullwidth');
            chatOpenBtn.style.display = 'none';
        });

        function addMessage(message, type) {
            const div = document.createElement('div');
            div.classList.add('chat-message', type);
            div.textContent = message;
            chatBody.appendChild(div);
            chatBody.scrollTop = chatBody.scrollHeight;
        }

        sendBtn.addEventListener('click', () => {
            const msg = input.value.trim();
            if (!msg) return;
            addMessage(msg, 'user-message');
            input.value = '';
            setTimeout(() => {
                addMessage('Respuesta automática del bot: ' + msg, 'bot-message');
            }, 500);
        });

        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendBtn.click();
        });
    });
  </script>

</body>
</html>