<?php
// Validación del usuario
include 'modelo/consultas_menu.php';

// URLs de los archivos CSV
$csv_vacantes_url = "http://localhost/Chatbot-AdminCenter/puestos.csv";
$csv_candidatos_url = "http://localhost/Chatbot-AdminCenter/candidatos.csv";

// Capturar parámetros desde la URL
$id_requisicion = $_GET['id_requisicion'] ?? 0;
$titulo_vacante = $_GET['titulo'] ?? '';

// Función para leer CSV desde URL
function leerCSVDesdeURL($url) {
    $datos = [];
    
    if (($handle = fopen($url, 'r')) !== FALSE) {
        $encabezados = fgetcsv($handle, 1000, ',');
        
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

// Obtener datos de ambos CSV
$vacantes = leerCSVDesdeURL($csv_vacantes_url);
$todos_candidatos = leerCSVDesdeURL($csv_candidatos_url);

// Si no hay candidatos, inicializar array vacío
if (empty($todos_candidatos)) {
    $todos_candidatos = [];
}

// Filtrar candidatos que coincidan con la vacante
$candidatos_filtrados = [];

if (!empty($titulo_vacante)) {
    $tituloBusqueda = trim(strtolower($titulo_vacante));
    
    foreach ($todos_candidatos as $candidato) {
        $tituloCandidato = trim(strtolower($candidato['Titulo'] ?? ''));
        
        // Buscar coincidencias en el título
        if (!empty($tituloCandidato) && 
            (strpos($tituloCandidato, $tituloBusqueda) !== false || 
             strpos($tituloBusqueda, $tituloCandidato) !== false ||
             similar_text($tituloBusqueda, $tituloCandidato) > 5)) {
            $candidatos_filtrados[] = $candidato;
        }
    }
} else {
    // Si no hay título, mostrar todos los candidatos
    $candidatos_filtrados = $todos_candidatos;
}

// Función para determinar criterio basado en el estado
function determinarCriterio($candidato) {
    $estado = $candidato['Estado'] ?? '';
    
    switch (strtolower($estado)) {
        case 'contratado':
        case 'listo para contratar':
            return 'Viable';
        case 'examen médico':
        case 'entrega de documentos':
        case 'carta oferta':
        case 'evaluaciones psicométricas':
        case 'entrevista reclutador':
        case 'prueba toxicológica':
            return 'Parcialmente viable';
        case 'requisition closed':
        case 'hired on other requisition':
        case 'descalificado por examen médico':
        case 'default':
            return 'No viable';
        default:
            return 'En evaluación';
    }
}

// Función para determinar estatus basado en el estado
function determinarEstatus($candidato) {
    $estado = $candidato['Estado'] ?? '';
    
    switch (strtolower($estado)) {
        case 'contratado':
        case 'listo para contratar':
            return 'Aceptado';
        case 'examen médico':
        case 'entrega de documentos':
        case 'carta oferta':
        case 'evaluaciones psicométricas':
        case 'entrevista reclutador':
        case 'prueba toxicológica':
            return 'En revisión';
        case 'requisition closed':
        case 'hired on other requisition':
        case 'descalificado por examen médico':
            return 'Rechazado';
        default:
            return 'Pendiente';
    }
}

// Función para obtener enlace de CV (simulada - ajusta según tu estructura real)
function obtenerEnlaceCV($candidato) {
    // Aquí puedes implementar la lógica para obtener el enlace real del CV
    // Por ahora devolvemos un enlace simulado
    if (!empty($candidato['Nombre']) && !empty($candidato['Apellido'])) {
        return "#"; // Reemplaza con la lógica real para obtener el CV
    }
    return null;
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
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css" rel="stylesheet">
  <title>Candidatos - <?php echo htmlspecialchars($titulo_vacante ?: 'Todos los candidatos'); ?></title>
  <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
  
  <style>
    /* Mantén todos los estilos CSS anteriores igual */
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
      transform: translateY(-1px);
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    }
    
    .table tbody td {
      padding: 15px 20px;
      border-color: #e9ecef;
      vertical-align: middle;
    }

    .viable { 
      background: #d4edda !important;
      border-left: 4px solid #28a745;
    }
    
    .parcial { 
      background: #fff3cd !important;
      border-left: 4px solid #ffc107;
    }
    
    .no-viable { 
      background: #f8d7da !important;
      border-left: 4px solid #dc3545;
    }

    .btn-download {
      background: #28a745;
      color: white;
      border: none;
      border-radius: 20px;
      padding: 8px 16px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(40, 167, 69, 0.2);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }
    
    .btn-back {
      background: #6c757d;
      color: white;
      border: none;
      border-radius: 25px;
      padding: 10px 25px;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 3px 10px rgba(108, 117, 125, 0.2);
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .candidate-link {
      color: #002B45;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      display: inline-flex;
      align-items: center;
      gap: 5px;
    }

    .status-badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-weight: 600;
      font-size: 12px;
      text-transform: uppercase;
    }
    
    .status-accepted {
      background: #28a745;
      color: white;
    }
    
    .status-review {
      background: #ffc107;
      color: white;
    }
    
    .status-rejected {
      background: #dc3545;
      color: white;
    }

    .criteria-badge {
      padding: 6px 12px;
      border-radius: 20px;
      font-weight: 600;
      font-size: 12px;
      color: white;
    }
    
    .criteria-viable {
      background: #28a745;
    }
    
    .criteria-parcial {
      background: #ffc107;
    }
    
    .criteria-no-viable {
      background: #dc3545;
    }
    
    .criteria-evaluacion {
      background: #17a2b8;
    }

    .email-text {
      font-size: 12px;
      color: #6c757d;
      display: block;
      margin-top: 2px;
    }

    /* Mantén el resto de los estilos igual */
  </style>
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
          <div class="user-info">
            <a href="https://billing.stripe.com/p/login/fZe3f33cggofeBy144" target="_blank">Actualizar Plan</a>
            <a class="a1" href="cerrarSesion.php">Cerrar Sesión</a>
          </div>
        </div>
      </div>
    </div>
  </header>


<main class="main-content">
  <!-- Header de la página -->
  <div class="page-header">
    <h2>
        <?php if (!empty($titulo_vacante)): ?>
            👥 Candidatos para: <?php echo htmlspecialchars($titulo_vacante); ?>
        <?php else: ?>
            👥 Todos los Candidatos
        <?php endif; ?>
    </h2>
    <?php if (!empty($titulo_vacante)): ?>
        <p class="mb-0">Requisición #<?php echo str_pad($id_requisicion, 3, '0', STR_PAD_LEFT); ?> | 
        <?php echo count($candidatos_filtrados); ?> candidato(s) encontrado(s)</p>
    <?php else: ?>
        <p class="mb-0"><?php echo count($candidatos_filtrados); ?> candidato(s) en total</p>
    <?php endif; ?>
  </div>

  <!-- Contenedor de la tabla -->
  <div class="table-container">
    <table id="tablaCandidatos" class="table table-striped table-bordered">
      <thead>
        <tr>
          <th>Nombre Completo</th>
          <th>Contacto</th>
          <th>Puesto Solicitado</th>
          <th>Estado</th>
          <th>Criterio</th>
          <th>Estatus</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($candidatos_filtrados)): ?>
            <?php foreach($candidatos_filtrados as $c): 
                $criterio = determinarCriterio($c);
                $estatus = determinarEstatus($c);
                
                $class = '';
                $criteria_class = '';
                $status_class = '';
                
                if($criterio == 'Viable') {
                    $class = 'viable';
                    $criteria_class = 'criteria-viable';
                } elseif($criterio == 'Parcialmente viable') {
                    $class = 'parcial';
                    $criteria_class = 'criteria-parcial';
                } elseif($criterio == 'No viable') {
                    $class = 'no-viable';
                    $criteria_class = 'criteria-no-viable';
                } else {
                    $class = '';
                    $criteria_class = 'criteria-evaluacion';
                }
                
                if($estatus == 'Aceptado') $status_class = 'status-accepted';
                elseif($estatus == 'En revisión') $status_class = 'status-review';
                elseif($estatus == 'Rechazado') $status_class = 'status-rejected';
                else $status_class = 'status-review';
                
                $nombreCompleto = trim($c['Nombre'] . ' ' . ($c['Apellido'] ?? ''));
            ?>
            <tr class="<?php echo $class; ?>">
              <td>
                <a href="#" class="candidate-link detalle-candidato" 
                   data-nombre="<?php echo htmlspecialchars($c['Nombre'] ?? ''); ?>" 
                   data-ap="<?php echo htmlspecialchars($c['Apellido'] ?? ''); ?>" 
                   data-email="<?php echo htmlspecialchars($c['Correo Electrónico'] ?? ''); ?>"
                   data-titulo="<?php echo htmlspecialchars($c['Titulo'] ?? ''); ?>"
                   data-estado="<?php echo htmlspecialchars($c['Estado'] ?? ''); ?>"
                   data-criterio="<?php echo $criterio; ?>" 
                   data-estatus="<?php echo $estatus; ?>">
                   <?php echo htmlspecialchars($nombreCompleto ?: 'Sin nombre'); ?>
                </a>
                <?php if (!empty($c['Correo Electrónico'])): ?>
                <span class="email-text">📧 <?php echo htmlspecialchars($c['Correo Electrónico']); ?></span>
                <?php endif; ?>
              </td>
              <td>
                <?php if (!empty($c['Correo Electrónico'])): ?>
                📧 <?php echo htmlspecialchars($c['Correo Electrónico']); ?>
                <?php else: ?>
                <span class="text-muted">Sin contacto</span>
                <?php endif; ?>
              </td>
              <td>
                <?php echo htmlspecialchars($c['Titulo'] ?? 'Sin puesto especificado'); ?>
              </td>
              <td>
                <?php echo htmlspecialchars($c['Estado'] ?? 'Sin estado'); ?>
              </td>
              <td>
                <span class="criteria-badge <?php echo $criteria_class; ?>">
                  <?php echo $criterio; ?>
                </span>
              </td>
              <td>
                <span class="status-badge <?php echo $status_class; ?>">
                  <?php echo $estatus; ?>
                </span>
              </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="text-muted">
                        <h5>No se encontraron candidatos</h5>
                        <p><?php echo empty($titulo_vacante) ? 'No hay candidatos registrados.' : 'No hay candidatos que coincidan con esta vacante.'; ?></p>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <a href="vacantes.php" class="btn-back">
    ← Volver a Vacantes
  </a>
</main>

<!-- Modal detalle candidato (actualizado) -->
<div class="modal fade" id="modalDetalleCandidato" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalNombre"></h5>
        <button type="button" class="close custom-close" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true" class="custom-close-icon">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row mb-4">
          <div class="col-md-6">
            <p><strong>📧 Email:</strong> <span id="modalEmail"></span></p>
            <p><strong>💼 Puesto Solicitado:</strong> <span id="modalTitulo"></span></p>
            <p><strong>📋 Estado Actual:</strong> <span id="modalEstado"></span></p>
          </div>
          <div class="col-md-6">
            <p><strong>🎯 Criterio:</strong> <span id="modalCriterio" class="criteria-badge"></span></p>
            <p><strong>📊 Estatus:</strong> <span id="modalEstatus" class="status-badge"></span></p>
          </div>
        </div>
        
        <hr>
        
        <div class="row">
          <div class="col-md-6">
            <h6>Vacantes Recomendadas</h6>
            <div class="evaluation-item">
              <strong>Desarrollador PHP Senior</strong><br>
              <small>95% de compatibilidad</small>
            </div>
            <div class="evaluation-item">
              <strong>Analista QA</strong><br>
              <small>87% de compatibilidad</small>
            </div>
          </div>
          <div class="col-md-6">
            <h6>🤖 Evaluaciones de IA</h6>
            <div class="evaluation-item">
              <strong>💻 Habilidades técnicas</strong><br>
              <span class="text-success">Excelente</span> (9.2/10)
            </div>
            <div class="evaluation-item">
              <strong>💬 Comunicación</strong><br>
              <span class="text-warning">Buena</span> (7.8/10)
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function() {
  $('#tablaCandidatos').DataTable({
    paging: true,
    pageLength: 10,
    lengthChange: true,
    searching: true,
    ordering: true,
    order: [[0, 'asc']],
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
    },
    dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
         '<"row"<"col-sm-12"tr>>' +
         '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
    buttons: [
      {
        extend: 'excelHtml5',
        text: 'Exportar a Excel',
        className: 'btn btn-excel',
        title: 'Candidatos_<?php echo $id_requisicion ? "Requisicion_" . $id_requisicion : "Todos"; ?>'
      }
    ]
  });

  // Abrir modal al hacer clic en el nombre
  $('.detalle-candidato').on('click', function(e){
    e.preventDefault();
    
    const nombre = $(this).data('nombre');
    const ap = $(this).data('ap');
    const email = $(this).data('email');
    const titulo = $(this).data('titulo');
    const estado = $(this).data('estado');
    const criterio = $(this).data('criterio');
    const estatus = $(this).data('estatus');
    
    $('#modalNombre').text(nombre + ' ' + ap);
    $('#modalEmail').text(email);
    $('#modalTitulo').text(titulo);
    $('#modalEstado').text(estado);
    $('#modalCriterio').text(criterio).addClass(getCriteriaClass(criterio));
    $('#modalEstatus').text(estatus).addClass(getStatusClass(estatus));
    
    $('#modalDetalleCandidato').modal('show');
  });

  function getCriteriaClass(criterio) {
    if (criterio === 'Viable') return 'criteria-viable';
    if (criterio === 'Parcialmente viable') return 'criteria-parcial';
    if (criterio === 'No viable') return 'criteria-no-viable';
    return 'criteria-evaluacion';
  }

  function getStatusClass(estatus) {
    if (estatus === 'Aceptado') return 'status-accepted';
    if (estatus === 'En revisión') return 'status-review';
    if (estatus === 'Rechazado') return 'status-rejected';
    return 'status-review';
  }
});
</script>

</body>
</html>