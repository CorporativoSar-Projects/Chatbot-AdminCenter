<?php
// candidatos_filtrados.php
include 'modelo/consultas_menu.php';

// URLs de los archivos CSV
$csv_candidatos_url = "http://localhost/Chatbot-AdminCenter/candidatos.csv";

// Capturar parámetros
$pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$id_requisicion = $_GET['id_requisicion'] ?? 0;
$titulo_vacante = $_GET['titulo'] ?? '';
$registros_por_pagina = 10;

// Función para leer el CSV desde la URL
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

function normaliza($texto) {
    $texto = mb_strtolower($texto, 'UTF-8');
    $texto = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $texto);
    return $texto;
}

function coincidenPorCategoria($tituloVacante, $tituloCandidato) {
    $tituloVacante = normaliza($tituloVacante);
    $tituloCandidato = normaliza($tituloCandidato);
    $categorias = [
        'administrativo' => [
            'vacantes' => ['consultor administrativo', 'auxiliar administrativo', 'asistente administrativo', 'dictaminador administrativo', 'mesa de control', 'responsable de turno', 'control documental', 'bóveda digital'],
            'candidatos' => ['asistente', 'auxiliar', 'oficinista', 'secretaria', 'coordinacion', 'administración', 'remisionista']
        ],
        'tecnico' => [
            'vacantes' => ['consultor técnico', 'analista técnico', 'especialista técnico', 'operador', 'operador tum', 'visitador', 'valuador', 'flebotomista', 'mecánico', 'montacarguista', 'ayudante general', 'ingeniero operador cnc'],
            'candidatos' => ['ingeniero', 'técnico', 'mecánico', 'operador', 'calidad', 'mantenimiento', 'montacarguista', 'seguridad industrial', 'almacén', 'logística', 'ayudante general', 'planta']
        ],
        'ventas' => [
            'vacantes' => ['promotor', 'asesor', 'consultor comercial', 'ejecutivo', 'representante', 'gerente comercial', 'especialista comercial', 'desarrollo canal masivo'],
            'candidatos' => ['ventas', 'comercial', 'promotor', 'vendedor', 'marketing', 'cuenta', 'agentes']
        ],
        'salud' => [
            'vacantes' => ['médico', 'médico general', 'médico dictaminador', 'médico contacto', 'médico especialista', 'flebotomista', 'auxiliar de farmacia', 'supervisor médico', 'gestión médica'],
            'candidatos' => ['médico', 'enfermero', 'farmacia', 'salud', 'clínico']
        ],
        'rh' => [
            'vacantes' => ['analista reclutamiento', 'especialista desarrollo', 'capacitacion', 'atracción de talento', 'analista capacitación'],
            'candidatos' => ['recursos humanos', 'rh', 'reclutamiento', 'capacitacion', 'talento', 'compensaciones', 'nominas']
        ],
        'ti' => [
            'vacantes' => ['líder análisis de requerimientos', 'soporte a proyectos', 'arquitectura aplicativa', 'incidentes de si', 'qa negocio', 'middle office', 'infraestructura', 'inteligencia y analíticos'],
            'candidatos' => ['sistemas', 'ti', 'software', 'programador', 'desarrollador', 'infraestructura', 'datos', 'it']
        ],
        'finanzas' => [
            'vacantes' => ['tesorería', 'contable', 'ingresos y egresos', 'control de cálculo y pago', 'análisis de crédito', 'transformación tesorería', 'pagos'],
            'candidatos' => ['finanzas', 'contabilidad', 'tesoreria', 'crédito', 'presupuesto']
        ],
        'suscripcion' => [
            'vacantes' => ['suscriptor', 'suscripción', 'suscriptor jr', 'suscriptor intermedio', 'reaseguro', 'normatividad técnica', 'oferta de valor', 'suscripción estratégica'],
            'candidatos' => ['seguros', 'suscripción', 'reaseguro', 'actuario', 'vida', 'autos']
        ],
        'siniestros_autos' => [
            'vacantes' => ['visitador de centros', 'ajustes', 'siniestros', 'centros de reparación', 'asesor de servicio automóvil', 'valuador', 'supervisor ajustes'],
            'candidatos' => ['automotriz', 'taller', 'autos', 'ajustes', 'siniestros', 'valuador']
        ],
        'analiticos' => [
            'vacantes' => ['analista indicadores', 'análisis de información', 'estadística', 'inteligencia de mercado', 'competitividad', 'indicadores'],
            'candidatos' => ['analista', 'datos', 'estadística', 'analítica', 'reportes']
        ],
        'operaciones' => [
            'vacantes' => ['servicio a clientes', 'representante centro de contacto', 'operación', 'bóveda digital', 'aplicación de primas'],
            'candidatos' => ['operaciones', 'servicio', 'contacto', 'call center', 'cliente']
        ],
        'gmm' => [
            'vacantes' => ['migración de condiciones gmm colectivo', 'programas gmm', 'prevención gmm'],
            'candidatos' => ['gmm', 'gastos médicos']
        ]
    ];

    foreach ($categorias as $categoria => $palabras) {
        $enVacante = false;
        $enCandidato = false;

        foreach ($palabras['vacantes'] as $palabra) {
            $palabra = normaliza($palabra);
            if (strpos($tituloVacante, $palabra) !== false) {
                $enVacante = true;
                break;
            }
        }

        foreach ($palabras['candidatos'] as $palabra) {
            $palabra = normaliza($palabra);
            if (strpos($tituloCandidato, $palabra) !== false) {
                $enCandidato = true;
                break;
            }
        }

        if ($enVacante && $enCandidato) {
            return true;
        }
    }

    return false;
}

// Función para determinar criterio
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

// Función para determinar estatus
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

// Obtener todos los candidatos
$todos_candidatos = leerCSVDesdeURL($csv_candidatos_url);
if (empty($todos_candidatos)) {
    $todos_candidatos = [];
}

// Filtrar candidatos por título de vacante
$candidatos_filtrados = [];
if (!empty($titulo_vacante)) {
    $tituloBusqueda = normaliza($titulo_vacante);
    
    foreach ($todos_candidatos as $candidato) {
        $tituloCandidatoRaw = $candidato['Titulo'] ?? '';
        $tituloCandidato = normaliza($tituloCandidatoRaw);

        if (empty($tituloCandidato)) {
            continue;
        }

        $coincide = false;

        if (strpos($tituloCandidato, $tituloBusqueda) !== false ||
            strpos($tituloBusqueda, $tituloCandidato) !== false ||
            similar_text($tituloBusqueda, $tituloCandidato) > 10) {
            $coincide = true;
        } else if (coincidenPorCategoria($tituloBusqueda, $tituloCandidato)) {
            $coincide = true;
        }

        if ($coincide) {
            $candidatos_filtrados[] = $candidato;
        }
    }
}

// Paginación
$total_candidatos = count($candidatos_filtrados);
$total_paginas = ceil($total_candidatos / $registros_por_pagina);
$inicio = ($pagina_actual - 1) * $registros_por_pagina;
$candidatos_paginados = array_slice($candidatos_filtrados, $inicio, $registros_por_pagina);
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css" />
    
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
            margin-bottom: 20px;
        }
        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }
        .viable { background: #d4edda !important; border-left: 4px solid #28a745; }
        .parcial { background: #fff3cd !important; border-left: 4px solid #ffc107; }
        .no-viable { background: #f8d7da !important; border-left: 4px solid #dc3545; }
        .criteria-badge { padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 12px; color: white; }
        .criteria-viable { background: #28a745; }
        .criteria-parcial { background: #ffc107; }
        .criteria-no-viable { background: #dc3545; }
        .criteria-evaluacion { background: #17a2b8; }
        .status-badge { padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 12px; text-transform: uppercase; }
        .status-accepted { background: #28a745; color: white; }
        .status-review { background: #ffc107; color: white; }
        .status-rejected { background: #dc3545; color: white; }
    </style>
    
    <title>Candidatos Filtrados</title>
</head>
<body>
    <!-- Logo y Navbar (igual que en tu página principal) -->
    <div class="rectangulo-container">
        <img src="img/LOGOTIPO_IXAH-02.png" width="70px" alt="Logo" class="img-logo-chiq" />
    </div>
    
    <header>
        <div class="user-dropdown">
            <!-- ... (mismo header que tu página principal) ... -->
        </div>
    </header>

    <main class="main-content">
        <!-- Header de la página -->
        <div class="page-header">
            <h2>👥 Candidatos para: <?php echo htmlspecialchars($titulo_vacante); ?></h2>
            <div class="stats-info" style="background: rgba(255, 255, 255, 0.1); padding: 10px 15px; border-radius: 10px; margin-top: 10px; font-size: 14px;">
                <strong>Requisición #<?php echo str_pad($id_requisicion, 3, '0', STR_PAD_LEFT); ?></strong> |
                <strong><?php echo count($candidatos_filtrados); ?> candidato(s) encontrado(s)</strong>
            </div>
        </div>

        <a href="vacantes_candidatos.php?tab=vacantes&pagina=1" class="btn-back">
            ← Volver a Vacantes
        </a>

        <!-- Contenedor de la tabla de candidatos -->
        <div class="table-container">
            <table id="tablaCandidatos" class="table table-striped table-bordered" style="width:100%">
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
                    <?php if (!empty($candidatos_paginados)): ?>
                        <?php foreach ($candidatos_paginados as $c):
                            $criterio = determinarCriterio($c);
                            $estatus = determinarEstatus($c);

                            $class = '';
                            $criteria_class = '';
                            $status_class = '';

                            if ($criterio == 'Viable') {
                                $class = 'viable';
                                $criteria_class = 'criteria-viable';
                            } elseif ($criterio == 'Parcialmente viable') {
                                $class = 'parcial';
                                $criteria_class = 'criteria-parcial';
                            } elseif ($criterio == 'No viable') {
                                $class = 'no-viable';
                                $criteria_class = 'criteria-no-viable';
                            } else {
                                $class = '';
                                $criteria_class = 'criteria-evaluacion';
                            }

                            if ($estatus == 'Aceptado') $status_class = 'status-accepted';
                            elseif ($estatus == 'En revisión') $status_class = 'status-review';
                            elseif ($estatus == 'Rechazado') $status_class = 'status-rejected';
                            else $status_class = 'status-review';

                            $nombreCompleto = trim($c['Nombre'] . ' ' . ($c['Apellido'] ?? ''));
                        ?>
                            <tr class="<?php echo $class; ?>">
                                <td>
                                    <?php echo htmlspecialchars($nombreCompleto ?: 'Sin nombre'); ?>
                                    <?php if (!empty($c['Correo Electrónico'])): ?>
                                        <span style="font-size: 12px; color: #6c757d; display: block; margin-top: 2px;">
                                            📧 <?php echo htmlspecialchars($c['Correo Electrónico']); ?>
                                        </span>
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
                                    <p>No hay candidatos que coincidan con esta vacante.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <?php if ($total_candidatos > $registros_por_pagina): ?>
            <div class="pagination-container">
                <ul class="pagination-custom">
                    <?php if ($pagina_actual > 1): ?>
                        <li class="page-item-custom">
                            <a class="page-link-custom" href="?id_requisicion=<?php echo urlencode($id_requisicion); ?>&titulo=<?php echo urlencode($titulo_vacante); ?>&pagina=<?php echo $pagina_actual - 1; ?>">
                                &laquo; Anterior
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item-custom">
                            <span class="page-link-custom disabled">&laquo; Anterior</span>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item-custom <?php echo $i == $pagina_actual ? 'active' : ''; ?>">
                            <a class="page-link-custom" href="?id_requisicion=<?php echo urlencode($id_requisicion); ?>&titulo=<?php echo urlencode($titulo_vacante); ?>&pagina=<?php echo $i; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagina_actual < $total_paginas): ?>
                        <li class="page-item-custom">
                            <a class="page-link-custom" href="?id_requisicion=<?php echo urlencode($id_requisicion); ?>&titulo=<?php echo urlencode($titulo_vacante); ?>&pagina=<?php echo $pagina_actual + 1; ?>">
                                Siguiente &raquo;
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item-custom">
                            <span class="page-link-custom disabled">Siguiente &raquo;</span>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tablaCandidatos').DataTable({
                paging: false,
                searching: true,
                ordering: true,
                order: [[0, 'asc']],
                language: {
                    "search": "Buscar:",
                    "zeroRecords": "No se encontraron registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                }
            });
        });
    </script>
</body>
</html>