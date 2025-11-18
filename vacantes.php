<?php
// Validación del usuario
include 'modelo/consultas_menu.php';

// URLs de los archivos CSV
$csv_vacantes_url = "http://localhost/Chatbot-AdminCenter/puestos.csv";
$csv_candidatos_url = "http://localhost/Chatbot-AdminCenter/candidatos.csv";

// Función para leer el CSV desde la URL
function leerCSVDesdeURL($url)
{
  $datos = [];

  // Intentar leer el archivo CSV
  if (($handle = fopen($url, 'r')) !== FALSE) {
    // Leer encabezados
    $encabezados = fgetcsv($handle, 1000, ',');

    // Leer cada fila de datos
    while (($fila = fgetcsv($handle, 1000, ',')) !== FALSE) {
      if (count($fila) === count($encabezados)) {
        $dato = array_combine($encabezados, $fila);
        $datos[] = $dato;
      }
    }
    fclose($handle);
  }

  return $datos;
}

// Función para contar candidatos que coinciden con el título de la vacante
function contarCandidatosPorVacante($candidatos, $tituloVacante)
{
  $contador = 0;
  $tituloLimpio = trim(strtolower($tituloVacante));

  foreach ($candidatos as $candidato) {
    $tituloCandidato = trim(strtolower($candidato['Titulo'] ?? ''));

    // Buscar coincidencias exactas o parciales en el título
    if (!empty($tituloCandidato)) {
      // Coincidencia exacta
      if ($tituloCandidato === $tituloLimpio) {
        $contador++;
      }
      // Coincidencia parcial (una contiene a la otra)
      else if (
        strpos($tituloCandidato, $tituloLimpio) !== false ||
        strpos($tituloLimpio, $tituloCandidato) !== false
      ) {
        $contador++;
      }
      // Coincidencia por similitud (para títulos similares pero no idénticos)
      else if (similar_text($tituloLimpio, $tituloCandidato) > 10) {
        $contador++;
      }
    }
  }

  return $contador;
}

// Obtener las vacantes del CSV
$vacantes = leerCSVDesdeURL($csv_vacantes_url);

// Obtener los candidatos del CSV
$candidatos = leerCSVDesdeURL($csv_candidatos_url);

// Si no hay candidatos, inicializar array vacío
if (empty($candidatos)) {
  $candidatos = [];
}

// Si no se puede leer el CSV de vacantes, mostrar mensaje de error
if (empty($vacantes)) {
  $error_csv = "No se pudieron cargar las vacantes desde el archivo CSV.";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/sty.css" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
  <link rel="shortcut icon" href="img/Logo_cabeza.svg" />

  <!-- DataTables CSS -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css" />

  <style>
    .main-content {
      margin-top: 100px;
      padding: 0 40px;
    }

    .page-header {
      background: #002B45;
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

    .dataTables_wrapper {
      padding: 20px;
    }

    .table thead th {
      background: #002B45;
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
      background: #002B45;
      color: white;
      border: none;
      border-radius: 25px;
      padding: 8px 20px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(0, 43, 69, 0.2);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .btn-candidates:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 43, 69, 0.3);
      color: white;
      text-decoration: none;
    }

    .btn-link {
      background: #28a745;
      color: white;
      border: none;
      border-radius: 20px;
      padding: 6px 15px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      margin-right: 8px;
    }

    .btn-link:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
      color: white;
      text-decoration: none;
    }

    .dataTables_paginate .paginate_button {
      border-radius: 8px !important;
      margin: 0 3px;
      border: 1px solid #dee2e6 !important;
    }

    .dataTables_paginate .paginate_button.current {
      background: #002B45 !important;
      color: white !important;
      border: none !important;
    }

    .dataTables_filter input {
      border-radius: 25px;
      border: 1px solid #c5c5c5;
      padding: 8px 15px;
      margin-left: 10px;
    }

    .dataTables_length select {
      border-radius: 8px;
      border: 1px solid #c5c5c5;
      padding: 5px;
    }

    .dt-buttons .btn {
      background: #002B45;
      color: white;
      border: none;
      border-radius: 8px;
      margin-right: 5px;
      transition: all 0.3s ease;
    }

    .dt-buttons .btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(0, 43, 69, 0.3);
    }

    .badge-count {
      background: #FF6B6B;
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

    .badge-count-zero {
      background: #6c757d;
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

    .requisicion-id {
      background: #f8f9fa;
      padding: 5px 10px;
      border-radius: 8px;
      font-weight: bold;
      color: #002B45;
      font-family: monospace;
    }

    .categoria-badge {
      background: #6f42c1;
      color: white;
      padding: 4px 12px;
      border-radius: 15px;
      font-size: 12px;
      font-weight: 600;
    }

    .ubicacion-badge {
      background: #fd7e14;
      color: white;
      padding: 4px 12px;
      border-radius: 15px;
      font-size: 12px;
      font-weight: 600;
    }

    .puesto-title {
      font-weight: 600;
      color: #002B45;
      font-size: 14px;
    }

    .actions-container {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
    }

    .error-message {
      background: #f8d7da;
      color: #721c24;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      margin-bottom: 20px;
    }

    .stats-info {
      background: rgba(255, 255, 255, 0.1);
      padding: 10px 15px;
      border-radius: 10px;
      margin-top: 10px;
      font-size: 14px;
    }

    /* Responsive Styles */
    @media (max-width: 1200px) {
      .main-content {
        padding: 0 30px;
      }
    }

    @media (max-width: 992px) {
      .main-content {
        padding: 0 20px;
      }
      
      .page-header {
        padding: 20px 25px;
      }
      
      .page-header h2 {
        font-size: 24px;
      }
      
      .stats-info {
        font-size: 13px;
      }
    }

    @media (max-width: 768px) {
      .main-content {
        margin-top: 80px;
        padding: 0 15px;
      }

      .page-header {
        padding: 20px;
        margin-bottom: 20px;
      }

      .page-header h2 {
        font-size: 22px;
      }
      
      .stats-info {
        font-size: 12px;
        padding: 8px 12px;
      }

      .table-container {
        border-radius: 10px;
      }
      
      .dataTables_wrapper {
        padding: 15px;
      }
      
      .actions-container {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
      }
      
      .btn-candidates, .btn-link {
        font-size: 12px;
        padding: 6px 12px;
      }
      
      .badge-count, .badge-count-zero {
        width: 20px;
        height: 20px;
        font-size: 10px;
      }
      
      .requisicion-id {
        font-size: 12px;
        padding: 3px 8px;
      }
      
      .categoria-badge, .ubicacion-badge {
        font-size: 11px;
        padding: 3px 8px;
      }
      
      .puesto-title {
        font-size: 13px;
      }
      
      .table thead th {
        padding: 10px 15px;
        font-size: 13px;
      }
      
      .table tbody td {
        padding: 10px 15px;
      }
    }

    @media (max-width: 576px) {
      .main-content {
        margin-top: 70px;
        padding: 0 10px;
      }
      
      .page-header {
        padding: 15px;
        border-radius: 10px;
      }
      
      .page-header h2 {
        font-size: 20px;
      }
      
      .stats-info {
        display: flex;
        flex-direction: column;
        gap: 5px;
      }
      
      .dataTables_wrapper {
        padding: 10px;
      }
      
      .dataTables_filter, .dataTables_length {
        margin-bottom: 10px;
      }
      
      .dataTables_filter input {
        width: 100% !important;
        margin-left: 0;
        margin-top: 5px;
      }
      
      .dt-buttons {
        text-align: center;
        margin-bottom: 10px;
      }
      
      .dt-buttons .btn {
        width: 100%;
        margin-bottom: 5px;
        margin-right: 0;
      }
      
      .table-responsive {
        border: none;
      }
      
      .table thead th {
        font-size: 12px;
        padding: 8px 10px;
      }
      
      .table tbody td {
        font-size: 12px;
        padding: 8px 10px;
      }
      
      .actions-container {
        gap: 3px;
      }
      
      .btn-candidates, .btn-link {
        font-size: 11px;
        padding: 5px 10px;
      }
    }

    @media (max-width: 400px) {
      .main-content {
        margin-top: 60px;
      }
      
      .page-header h2 {
        font-size: 18px;
      }
      
      .stats-info {
        font-size: 11px;
      }
      
      .requisicion-id {
        font-size: 11px;
      }
      
      .categoria-badge, .ubicacion-badge {
        font-size: 10px;
      }
      
      .puesto-title {
        font-size: 12px;
      }
    }

    /* DataTables Responsive Adjustments */
    .dtr-data {
      padding-left: 10px !important;
    }
    
    .dtr-title {
      font-weight: 600;
      min-width: 100px;
    }
  </style>

  <title>Vacantes - Admin</title>
</head>

<body>

  <!-- Logo y Navbar -->
  <div class="rectangulo-container">
    <img src="img/Logo_cabeza.svg" width="70px" alt="Logo" class="img-logo-chiq" />
  </div>

  <header>
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

  <main class="main-content">
    <!-- Header de la página -->
    <div class="page-header">
      <h2>📊 Vacantes Activas</h2>
      <?php if (isset($error_csv)): ?>
        <p class="mb-0" style="opacity: 0.8; font-size: 14px;">Error al cargar datos del CSV</p>
      <?php else: ?>
        <div class="stats-info">
          <strong><?php echo count($vacantes); ?> vacantes activas</strong> |
          <strong><?php echo count($candidatos); ?> candidatos en total</strong>
        </div>
      <?php endif; ?>
    </div>

    <!-- Mensaje de error si no se puede cargar el CSV -->
    <?php if (isset($error_csv)): ?>
      <div class="error-message">
        <h5>❌ Error al cargar las vacantes</h5>
        <p><?php echo $error_csv; ?></p>
        <p><small>Verifica que el archivo CSV esté disponible en: <?php echo $csv_vacantes_url; ?></small></p>
      </div>
    <?php endif; ?>

    <!-- Contenedor de la tabla -->
    <div class="table-container">
      <div class="table-responsive">
        <table id="tablaVacantes" class="table table-striped table-bordered dt-responsive nowrap" width="100%">
          <thead>
            <tr>
              <th>ID Requisición</th>
              <th>Puesto</th>
              <th>Categoría</th>
              <th>Ubicación</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($vacantes)): ?>
              <?php foreach ($vacantes as $vacante):
                // Calcular número REAL de candidatos que coinciden con esta vacante
                $candidateCount = contarCandidatosPorVacante($candidatos, $vacante['Titulo']);
                $badgeClass = $candidateCount > 0 ? 'badge-count' : 'badge-count-zero';
              ?>
                <tr>
                  <td>
                    <span class="requisicion-id">#<?php echo htmlspecialchars($vacante['ID de requisición de personal']); ?></span>
                  </td>
                  <td>
                    <div class="puesto-title"><?php echo htmlspecialchars($vacante['Titulo']); ?></div>
                  </td>
                  <td>
                    <span class="categoria-badge"><?php echo htmlspecialchars($vacante['Categoría']); ?></span>
                  </td>
                  <td>
                    <span class="ubicacion-badge">📍 <?php echo htmlspecialchars($vacante['Ubicación']); ?></span>
                  </td>
                  <td>
                    <div class="actions-container">
                      <?php if (!empty($vacante['link'])): ?>
                        <a href="<?php echo htmlspecialchars($vacante['link']); ?>" target="_blank" class="btn-link">
                          🔗 Ver Vacante
                        </a>
                      <?php endif; ?>

                      <?php if ($candidateCount > 0): ?>
                        <!-- Si HAY candidatos, enlace normal -->
                        <a href="candidatos.php?id_requisicion=<?php echo urlencode($vacante['ID de requisición de personal']); ?>&titulo=<?php echo urlencode($vacante['Titulo']); ?>" class="btn btn-candidates">
                          👥 Candidatos <span class="<?php echo $badgeClass; ?>"><?php echo $candidateCount; ?></span>
                        </a>
                      <?php else: ?>
                        <!-- Si NO HAY candidatos, botón deshabilitado -->
                        <span class="btn btn-candidates" style="opacity: 0.6; cursor: not-allowed;">
                          👥 Candidatos <span class="<?php echo $badgeClass; ?>">0</span>
                        </span>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-center py-4">
                  <div class="text-muted">
                    <h5>No hay vacantes disponibles</h5>
                    <p>No se pudieron cargar las vacantes desde el archivo CSV.</p>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- DataTables JS -->
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

  <script>
    $(document).ready(function() {
      $('#tablaVacantes').DataTable({
        paging: true,
        pageLength: 10,
        lengthChange: true,
        searching: true,
        ordering: true,
        order: [
          [0, 'asc']
        ],
        responsive: {
          details: {
            display: $.fn.dataTable.Responsive.display.modal({
              header: function(row) {
                var data = row.data();
                return 'Detalles de Vacante: ' + data[1];
              }
            }),
            renderer: $.fn.dataTable.Responsive.renderer.tableAll({
              tableClass: 'table'
            })
          }
        },
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
        },
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
          '<"row"<"col-sm-12"tr>>' +
          '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [{
          extend: 'excelHtml5',
          text: '📊 Exportar a Excel',
          className: 'btn btn-excel',
          title: 'Vacantes_Activas_' + new Date().toISOString().slice(0, 10)
        }]
      });
    });
  </script>
</body>

</html>