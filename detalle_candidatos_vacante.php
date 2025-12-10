<?php
// Validación del usuario
include 'modelo/consultas_menu.php';

// URLs de los archivos CSV
$csv_vacantes_url = "http://localhost/Chatbot-AdminCenter/puestos.csv";
$csv_candidatos_url = "http://localhost/Chatbot-AdminCenter/candidatos.csv";

// Determinar qué pestaña está activa
$tab_activa = isset($_GET['tab']) ? $_GET['tab'] : 'vacantes';
$pagina_actual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$registros_por_pagina = 10;

// Capturar parámetros para candidatos
$id_requisicion = $_GET['id_requisicion'] ?? 0;
$titulo_vacante = $_GET['titulo'] ?? '';

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

    if (empty($tituloVacante)) {
        return 0;
    }

    // Normalizar una sola vez
    $tituloBusqueda = normaliza($tituloVacante);

    foreach ($candidatos as $candidato) {

        $tituloCandidatoRaw = $candidato['Titulo'] ?? '';
        $tituloCandidato = normaliza($tituloCandidatoRaw);

        if (empty($tituloCandidato)) {
            continue;
        }

        // 1. Coincidencia exacta o parcial real
        if (
            strpos($tituloCandidato, $tituloBusqueda) !== false ||
            strpos($tituloBusqueda, $tituloCandidato) !== false ||
            similar_text($tituloBusqueda, $tituloCandidato) > 10
        ) {
            $contador++;
        }

        // 2. Coincidencia por categoría
        else if (coincidenPorCategoria($tituloBusqueda, $tituloCandidato)) {
            $contador++;
        }
    }

    return $contador;
}

// Función para contar candidatos para TODAS las vacantes
function contarCandidatosPorTodasVacantes($vacantes, $candidatos)
{
    $resultados = [];
    
    foreach ($vacantes as $vacante) {
        $titulo = $vacante['title_ix'] ?? $vacante['Titulo'] ?? '';
        if (!empty($titulo)) {
            $resultados[$titulo] = contarCandidatosPorVacante($candidatos, $titulo);
        }
    }
    
    // Ordenar por cantidad de candidatos (mayor a menor)
    arsort($resultados);
    
    return $resultados;
}

function normaliza($texto)
{
    $texto = mb_strtolower($texto, 'UTF-8');
    $texto = str_replace(
        ['á', 'é', 'í', 'ó', 'ú', 'ñ'],
        ['a', 'e', 'i', 'o', 'u', 'n'],
        $texto
    );
    return $texto;
}

function coincidenPorCategoria($tituloVacante, $tituloCandidato)
{
    $tituloVacante = normaliza($tituloVacante);
    $tituloCandidato = normaliza($tituloCandidato);
    $categorias = [
        'administrativo' => [
            'vacantes' => [
                'consultor administrativo',
                'auxiliar administrativo',
                'asistente administrativo',
                'dictaminador administrativo',
                'mesa de control',
                'responsable de turno',
                'control documental',
                'bóveda digital'
            ],
            'candidatos' => [
                'asistente',
                'auxiliar',
                'oficinista',
                'secretaria',
                'coordinacion',
                'administración',
                'remisionista'
            ]
        ],
        'tecnico' => [
            'vacantes' => [
                'consultor técnico',
                'analista técnico',
                'especialista técnico',
                'operador',
                'operador tum',
                'visitador',
                'valuador',
                'flebotomista',
                'mecánico',
                'montacarguista',
                'ayudante general',
                'ingeniero operador cnc'
            ],
            'candidatos' => [
                'ingeniero',
                'técnico',
                'mecánico',
                'operador',
                'calidad',
                'mantenimiento',
                'montacarguista',
                'seguridad industrial',
                'almacén',
                'logística',
                'ayudante general',
                'planta'
            ]
        ],
        'ventas' => [
            'vacantes' => [
                'promotor',
                'asesor',
                'consultor comercial',
                'ejecutivo',
                'representante',
                'gerente comercial',
                'especialista comercial',
                'desarrollo canal masivo'
            ],
            'candidatos' => [
                'ventas',
                'comercial',
                'promotor',
                'vendedor',
                'marketing',
                'cuenta',
                'agentes'
            ]
        ],
        'salud' => [
            'vacantes' => [
                'médico',
                'médico general',
                'médico dictaminador',
                'médico contacto',
                'médico especialista',
                'flebotomista',
                'auxiliar de farmacia',
                'supervisor médico',
                'gestión médica'
            ],
            'candidatos' => [
                'médico',
                'enfermero',
                'farmacia',
                'salud',
                'clínico'
            ]
        ],
        'rh' => [
            'vacantes' => [
                'analista reclutamiento',
                'especialista desarrollo',
                'capacitacion',
                'atracción de talento',
                'analista capacitación'
            ],
            'candidatos' => [
                'recursos humanos',
                'rh',
                'reclutamiento',
                'capacitacion',
                'talento',
                'compensaciones',
                'nominas'
            ]
        ],
        'ti' => [
            'vacantes' => [
                'líder análisis de requerimientos',
                'soporte a proyectos',
                'arquitectura aplicativa',
                'incidentes de si',
                'qa negocio',
                'middle office',
                'infraestructura',
                'inteligencia y analíticos'
            ],
            'candidatos' => [
                'sistemas',
                'ti',
                'software',
                'programador',
                'desarrollador',
                'infraestructura',
                'datos',
                'it'
            ]
        ],
        'finanzas' => [
            'vacantes' => [
                'tesorería',
                'contable',
                'ingresos y egresos',
                'control de cálculo y pago',
                'análisis de crédito',
                'transformación tesorería',
                'pagos'
            ],
            'candidatos' => [
                'finanzas',
                'contabilidad',
                'tesoreria',
                'crédito',
                'presupuesto'
            ]
        ],
        'suscripcion' => [
            'vacantes' => [
                'suscriptor',
                'suscripción',
                'suscriptor jr',
                'suscriptor intermedio',
                'reaseguro',
                'normatividad técnica',
                'oferta de valor',
                'suscripción estratégica'
            ],
            'candidatos' => [
                'seguros',
                'suscripción',
                'reaseguro',
                'actuario',
                'vida',
                'autos'
            ]
        ],
        'siniestros_autos' => [
            'vacantes' => [
                'visitador de centros',
                'ajustes',
                'siniestros',
                'centros de reparación',
                'asesor de servicio automóvil',
                'valuador',
                'supervisor ajustes'
            ],
            'candidatos' => [
                'automotriz',
                'taller',
                'autos',
                'ajustes',
                'siniestros',
                'valuador'
            ]
        ],
        'analiticos' => [
            'vacantes' => [
                'analista indicadores',
                'análisis de información',
                'estadística',
                'inteligencia de mercado',
                'competitividad',
                'indicadores'
            ],
            'candidatos' => [
                'analista',
                'datos',
                'estadística',
                'analítica',
                'reportes'
            ]
        ],
        'operaciones' => [
            'vacantes' => [
                'servicio a clientes',
                'representante centro de contacto',
                'operación',
                'bóveda digital',
                'aplicación de primas'
            ],
            'candidatos' => [
                'operaciones',
                'servicio',
                'contacto',
                'call center',
                'cliente'
            ]
        ],
        'gmm' => [
            'vacantes' => [
                'migración de condiciones gmm colectivo',
                'programas gmm',
                'prevención gmm'
            ],
            'candidatos' => [
                'gmm',
                'gastos médicos'
            ]
        ]
    ];

    foreach ($categorias as $categoria => $palabras) {
        $enVacante = false;
        $enCandidato = false;

        // palabras exactas en vacante
        foreach ($palabras['vacantes'] as $palabra) {
            $palabra = normaliza($palabra);
            if (strpos($tituloVacante, $palabra) !== false) {
                $enVacante = true;
                break;
            }
        }

        // palabras exactas en candidato
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

// Función para determinar criterio basado en el estado
function determinarCriterio($candidato)
{
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
function determinarEstatus($candidato)
{
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

// Obtener las vacantes del CSV
$vacantes = leerCSVDesdeURL($csv_vacantes_url);

// Obtener los candidatos del CSV
$todos_candidatos = leerCSVDesdeURL($csv_candidatos_url);

// Si no hay candidatos, inicializar array vacío
if (empty($todos_candidatos)) {
    $todos_candidatos = [];
}

// Si no se puede leer el CSV de vacantes, mostrar mensaje de error
if (empty($vacantes)) {
    $error_csv = "No se pudieron cargar las vacantes desde el archivo CSV.";
}

// Procesar candidatos para la pestaña activa
if ($tab_activa == 'candidatos') {
    // Filtrar candidatos que coincidan con la vacante (si hay parámetros)
    $candidatos_filtrados = [];

    if (!empty($titulo_vacante)) {
        // Normalizar una sola vez
        $tituloBusqueda = normaliza($titulo_vacante);

        foreach ($todos_candidatos as $candidato) {
            $tituloCandidatoRaw = $candidato['Titulo'] ?? '';
            $tituloCandidato = normaliza($tituloCandidatoRaw);

            if (empty($tituloCandidato)) {
                continue;
            }

            $coincide = false;

            // 1. Coincidencia exacta o parcial REAL
            if (
                strpos($tituloCandidato, $tituloBusqueda) !== false ||
                strpos($tituloBusqueda, $tituloCandidato) !== false ||
                similar_text($tituloBusqueda, $tituloCandidato) > 10
            ) {
                $coincide = true;
            }
            // 2. Coincidencia por categoría
            else if (coincidenPorCategoria($tituloBusqueda, $tituloCandidato)) {
                $coincide = true;
            }

            if ($coincide) {
                $candidatos_filtrados[] = $candidato;
            }
        }
    } else {
        // Si no hay título, mostrar todos
        $candidatos_filtrados = $todos_candidatos;
    }

    // Paginación para candidatos
    $total_candidatos = count($candidatos_filtrados);
    $total_paginas = ceil($total_candidatos / $registros_por_pagina);
    $inicio = ($pagina_actual - 1) * $registros_por_pagina;
    $candidatos_paginados = array_slice($candidatos_filtrados, $inicio, $registros_por_pagina);
} else {
    // Para vacantes, usar todos los candidatos para contar
    $candidatos = $todos_candidatos;

    // Paginación para vacantes
    $total_vacantes = count($vacantes);
    $total_paginas = ceil($total_vacantes / $registros_por_pagina);
    $inicio = ($pagina_actual - 1) * $registros_por_pagina;
    $vacantes_paginadas = array_slice($vacantes, $inicio, $registros_por_pagina);
    
    // Contar candidatos por cada vacante
    $conteo_candidatos_por_vacante = contarCandidatosPorTodasVacantes($vacantes, $todos_candidatos);
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

        .tabs-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            padding: 0 30px;
        }

        .tab-btn {
            padding: 12px 30px;
            border: none;
            background: #f8f9fa;
            font-size: 16px;
            font-weight: 600;
            color: #6c757d;
            cursor: pointer;
            border-radius: 8px 8px 0 0;
            transition: all 0.3s ease;
        }

        .tab-btn:hover {
            background: #e9ecef;
            color: #495057;
        }

        .tab-btn.active {
            background: #002B45;
            color: white;
            box-shadow: 0 2px 10px rgba(0, 43, 69, 0.2);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
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

        /* Estilos para Vacantes */
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

        .badge-count-list {
            background: #FF6B6B;
            color: white;
            border-radius: 12px;
            padding: 2px 8px;
            font-size: 12px;
            font-weight: bold;
            margin-left: 5px;
            min-width: 25px;
            text-align: center;
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

        /* Estilos para Candidatos */
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

        /* Paginación personalizada */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .pagination-custom {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .page-item-custom {
            margin: 0 3px;
        }

        .page-link-custom {
            padding: 8px 16px;
            border: 1px solid #dee2e6;
            background-color: white;
            color: #007bff;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s ease;
        }

        .page-link-custom:hover {
            background-color: #e9ecef;
        }

        .page-item-custom.active .page-link-custom {
            background-color: #002B45;
            color: white;
            border-color: #002B45;
        }

        .page-link-custom.disabled {
            color: #6c757d;
            pointer-events: none;
            opacity: 0.6;
        }

        .stats-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 15px;
            border-radius: 10px;
            margin-top: 10px;
            font-size: 14px;
        }

        /* Estilo para el resumen de candidatos */
        .resumen-candidatos {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            border-left: 4px solid #002B45;
        }

        .resumen-title {
            color: #002B45;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .resumen-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }

        .resumen-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .resumen-item-title {
            font-weight: 600;
            color: #495057;
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .resumen-item-count {
            background: #002B45;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
        }

        .resumen-item-detail {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }

        /* AREA DEL CHAT (si decides mantenerlo) */
        #chat-panel {
            width: 25%;
            background-color: #f3f3f3;
            border-left: 1px solid #c5c5c5;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            box-shadow: -4px 0px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            height: calc(100vh - 100px);
            position: fixed;
            right: 0;
            top: 100px;
            z-index: 1000;
        }

        #chat-header {
            background: #002B45;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: "Montserrat", sans-serif;
            font-weight: bold;
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
            background: #002B45;
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

        .special-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin: 15px 0;
        }

        .special-btn {
            background: #002B45;
            color: white;
            border: none;
            border-radius: 25px;
            padding: 12px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            font-family: "Montserrat", sans-serif;
        }

        .special-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 43, 69, 0.3);
        }

        .analysis-input-container {
            margin: 15px 0;
            display: none;
        }

        .analysis-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #c5c5c5;
            border-radius: 25px;
            font-family: "Montserrat", sans-serif;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .analysis-btn {
            background: #28a745;
            color: white;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
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
            background: #002B45;
            color: white;
            cursor: pointer;
            font-family: "Montserrat", sans-serif;
            font-weight: bold;
        }

        #chat-open-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 70px;
            height: 70px;
            background: #002B45;
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

        #chat-panel.hidden {
            width: 0;
            overflow: hidden;
            border-left: none;
            padding: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-top: 80px;
                padding: 0 15px;
            }

            .tabs-container {
                padding: 0 15px;
            }

            .tab-btn {
                padding: 10px 20px;
                font-size: 14px;
            }

            .page-header {
                padding: 20px;
            }

            .page-header h2 {
                font-size: 22px;
            }

            .resumen-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <title>Vacantes y Candidatos - Admin</title>
</head>

<body>

    <!-- Logo y Navbar -->
    <div class="rectangulo-container">
        <img
            src="img/LOGOTIPO_IXAH-02.png"
            width="70px"
            alt="Logo"
            class="img-logo-chiq" />
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
                        <a href="menu.php">Menu</a>
                    </div>
                    <div class="user-info">
                        <a href="vacantes_candidatos.php?tab=vacantes">Vacantes y Candidatos</a>
                    </div>
                    <div class="user-info">
                        <a href="panel_admin_ia.php">Panel de Configuración</a>
                    </div>
                    <div class="user-info">
                        <a href="tokens.php">Tokens</a>
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

    <main class="main-content">
        <!-- Pestañas -->
        <div class="tabs-container">
            <button class="tab-btn <?php echo $tab_activa == 'vacantes' ? 'active' : ''; ?>"
                onclick="cambiarTab('vacantes', 1)">
                📋 Vacantes
            </button>
            <button class="tab-btn <?php echo $tab_activa == 'candidatos' ? 'active' : ''; ?>"
                onclick="cambiarTab('candidatos', 1)">
                👥 Candidatos
            </button>
        </div>

        <!-- Resumen de Candidatos por Vacante (solo en pestaña de vacantes) -->
        <?php if ($tab_activa == 'vacantes' && !empty($conteo_candidatos_por_vacante)): ?>
            <div class="resumen-candidatos">
                <div class="resumen-title">
                    📊 Resumen de Candidatos por Puesto
                    <small style="font-size: 14px; font-weight: normal; color: #6c757d;">(Total: <?php echo array_sum($conteo_candidatos_por_vacante); ?> coincidencias)</small>
                </div>
                <div class="resumen-grid">
                    <?php 
                    $top_10 = array_slice($conteo_candidatos_por_vacante, 0, 10, true);
                    foreach ($top_10 as $titulo => $cantidad): 
                        if ($cantidad > 0): // Solo mostrar vacantes con candidatos
                    ?>
                        <div class="resumen-item">
                            <div class="resumen-item-title">
                                <?php echo htmlspecialchars(substr($titulo, 0, 30)) . (strlen($titulo) > 30 ? '...' : ''); ?>
                                <span class="resumen-item-count"><?php echo $cantidad; ?></span>
                            </div>
                            <div class="resumen-item-detail">
                                <?php echo $cantidad; ?> candidato(s) compatible(s)
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    
                    // Contar vacantes sin candidatos
                    $vacantes_sin_candidatos = 0;
                    foreach ($conteo_candidatos_por_vacante as $cantidad) {
                        if ($cantidad == 0) {
                            $vacantes_sin_candidatos++;
                        }
                    }
                    
                    if ($vacantes_sin_candidatos > 0): ?>
                        <div class="resumen-item">
                            <div class="resumen-item-title">
                                Vacantes sin candidatos
                                <span class="resumen-item-count" style="background: #6c757d;"><?php echo $vacantes_sin_candidatos; ?></span>
                            </div>
                            <div class="resumen-item-detail">
                                Sin candidatos compatibles
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Header de la página (depende de la pestaña) -->
        <div class="page-header">
            <?php if ($tab_activa == 'vacantes'): ?>
                <h2>📊 Vacantes Activas</h2>
                <?php if (isset($error_csv)): ?>
                    <p class="mb-0" style="opacity: 0.8; font-size: 14px;">Error al cargar datos del CSV</p>
                <?php else: ?>
                    <div class="stats-info">
                        <strong><?php echo count($vacantes); ?> vacantes activas</strong> |
                        <strong><?php echo count($todos_candidatos); ?> candidatos en total</strong> |
                        <strong><?php echo array_sum($conteo_candidatos_por_vacante); ?> coincidencias encontradas</strong>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <h2>
                    <?php if (!empty($titulo_vacante)): ?>
                        👥 Candidatos para: <?php echo htmlspecialchars($titulo_vacante); ?>
                    <?php else: ?>
                        👥 Todos los Candidatos
                    <?php endif; ?>
                </h2>
                <?php if (!empty($titulo_vacante)): ?>
                    <div class="stats-info">
                        <strong>Requisición #<?php echo str_pad($id_requisicion, 3, '0', STR_PAD_LEFT); ?></strong> |
                        <strong><?php echo count($candidatos_filtrados); ?> candidato(s) encontrado(s)</strong>
                    </div>
                <?php else: ?>
                    <div class="stats-info">
                        <strong><?php echo count($candidatos_filtrados); ?> candidato(s) en total</strong>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Contenido de Vacantes -->
        <div id="vacantes-content" class="tab-content <?php echo $tab_activa == 'vacantes' ? 'active' : ''; ?>">
            <?php if (isset($error_csv)): ?>
                <div class="error-message" style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 10px; text-align: center; margin-bottom: 20px;">
                    <h5>❌ Error al cargar las vacantes</h5>
                    <p><?php echo $error_csv; ?></p>
                    <p><small>Verifica que el archivo CSV esté disponible en: <?php echo $csv_vacantes_url; ?></small></p>
                </div>
            <?php endif; ?>

            <!-- Contenedor de la tabla de vacantes -->
            <div class="table-container">
                <div class="table-responsive">
                    <table id="tablaVacantes" class="table table-striped table-bordered" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID Requisición</th>
                                <th>Puesto</th>
                                <th>Candidatos</th>
                                <th>Categoría</th>
                                <th>Ubicación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($vacantes_paginadas)): ?>
                                <?php foreach ($vacantes_paginadas as $vacante):
                                    $idRequisicion = $vacante['reqId_ix'] ?? $vacante['ID de requisición de personal'] ?? '';
                                    $titulo = $vacante['title_ix'] ?? $vacante['Titulo'] ?? '';
                                    $categoria = $vacante['category_ix'] ?? $vacante['Categoría'] ?? '';
                                    $ubicacion = $vacante['location_ix'] ?? $vacante['Ubicación'] ?? '';
                                    $link = $vacante['link'] ?? '#';
                                    
                                    // Obtener cantidad de candidatos para esta vacante
                                    $candidateCount = isset($conteo_candidatos_por_vacante[$titulo]) ? $conteo_candidatos_por_vacante[$titulo] : 0;
                                    $badgeClass = $candidateCount > 0 ? 'badge-count' : 'badge-count-zero';
                                ?>
                                    <tr>
                                        <td>
                                            <span class="requisicion-id">#<?php echo htmlspecialchars($idRequisicion); ?></span>
                                        </td>
                                        <td>
                                            <div class="puesto-title"><?php echo htmlspecialchars($titulo); ?></div>
                                        </td>
                                        <td>
                                            <?php if ($candidateCount > 0): ?>
                                                <span style="display: inline-flex; align-items: center;">
                                                    <span class="badge-count-list"><?php echo $candidateCount; ?></span>
                                                    <small style="margin-left: 5px; color: #6c757d;">candidato(s)</small>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Sin candidatos</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="categoria-badge"><?php echo htmlspecialchars($categoria); ?></span>
                                        </td>
                                        <td>
                                            <span class="ubicacion-badge">📍 <?php echo htmlspecialchars($ubicacion); ?></span>
                                        </td>
                                        <td>
                                            <div class="actions-container">
                                                <?php if (!empty($link) && $link != '#'): ?>
                                                    <a href="<?php echo htmlspecialchars($link); ?>" target="_blank" class="btn-link">
                                                        🔗 Ver Vacante
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($candidateCount > 0): ?>
                                                    <!-- Enlace a la pestaña de candidatos filtrados -->
                                                    <a href="?tab=candidatos&pagina=1&id_requisicion=<?php echo urlencode($idRequisicion); ?>&titulo=<?php echo urlencode($titulo); ?>" class="btn btn-candidates">
                                                        👥 Ver Candidatos <span class="<?php echo $badgeClass; ?>"><?php echo $candidateCount; ?></span>
                                                    </a>
                                                <?php else: ?>
                                                    <!-- Si NO HAY candidatos, mostrar 0 pero SIN redirección -->
                                                    <span class="btn btn-candidates" style="opacity: 0.6; cursor: default;">
                                                        👥 Ver Candidatos <span class="<?php echo $badgeClass; ?>">0</span>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
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

            <!-- Paginación para Vacantes -->
            <?php if ($total_vacantes > $registros_por_pagina): ?>
                <div class="pagination-container">
                    <ul class="pagination-custom">
                        <!-- Botón Anterior -->
                        <?php if ($pagina_actual > 1): ?>
                            <li class="page-item-custom">
                                <a class="page-link-custom" href="?tab=vacantes&pagina=<?php echo $pagina_actual - 1; ?>">
                                    &laquo; Anterior
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item-custom">
                                <span class="page-link-custom disabled">&laquo; Anterior</span>
                            </li>
                        <?php endif; ?>

                        <!-- Números de página -->
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item-custom <?php echo $i == $pagina_actual ? 'active' : ''; ?>">
                                <a class="page-link-custom" href="?tab=vacantes&pagina=<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Botón Siguiente -->
                        <?php if ($pagina_actual < $total_paginas): ?>
                            <li class="page-item-custom">
                                <a class="page-link-custom" href="?tab=vacantes&pagina=<?php echo $pagina_actual + 1; ?>">
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
        </div>

        <!-- Contenido de Candidatos -->
        <div id="candidatos-content" class="tab-content <?php echo $tab_activa == 'candidatos' ? 'active' : ''; ?>">
            <?php if (!empty($titulo_vacante)): ?>
                <a href="?tab=vacantes&pagina=1" class="btn-back">
                    ← Volver a Vacantes
                </a>
            <?php endif; ?>

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

            <!-- Paginación para Candidatos -->
            <?php if ($total_candidatos > $registros_por_pagina): ?>
                <div class="pagination-container">
                    <ul class="pagination-custom">
                        <!-- Botón Anterior -->
                        <?php if ($pagina_actual > 1): ?>
                            <li class="page-item-custom">
                                <a class="page-link-custom" href="?tab=candidatos&pagina=<?php echo $pagina_actual - 1; ?><?php echo !empty($id_requisicion) ? '&id_requisicion=' . urlencode($id_requisicion) : ''; ?><?php echo !empty($titulo_vacante) ? '&titulo=' . urlencode($titulo_vacante) : ''; ?>">
                                    &laquo; Anterior
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item-custom">
                                <span class="page-link-custom disabled">&laquo; Anterior</span>
                            </li>
                        <?php endif; ?>

                        <!-- Números de página -->
                        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                            <li class="page-item-custom <?php echo $i == $pagina_actual ? 'active' : ''; ?>">
                                <a class="page-link-custom" href="?tab=candidatos&pagina=<?php echo $i; ?><?php echo !empty($id_requisicion) ? '&id_requisicion=' . urlencode($id_requisicion) : ''; ?><?php echo !empty($titulo_vacante) ? '&titulo=' . urlencode($titulo_vacante) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>

                        <!-- Botón Siguiente -->
                        <?php if ($pagina_actual < $total_paginas): ?>
                            <li class="page-item-custom">
                                <a class="page-link-custom" href="?tab=candidatos&pagina=<?php echo $pagina_actual + 1; ?><?php echo !empty($id_requisicion) ? '&id_requisicion=' . urlencode($id_requisicion) : ''; ?><?php echo !empty($titulo_vacante) ? '&titulo=' . urlencode($titulo_vacante) : ''; ?>">
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
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- Modal detalle candidato -->
    <div class="modal fade" id="modalDetalleCandidato" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNombre"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
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

    <script>
        $(document).ready(function() {
            // Inicializar DataTables para la tabla visible
            if ($('#vacantes-content').hasClass('active')) {
                inicializarDataTableVacantes();
            } else if ($('#candidatos-content').hasClass('active')) {
                inicializarDataTableCandidatos();
            }

            // Abrir modal al hacer clic en el nombre del candidato
            $(document).on('click', '.detalle-candidato', function(e) {
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

                // Limpiar clases anteriores
                $('#modalCriterio').removeClass().addClass('criteria-badge ' + getCriteriaClass(criterio)).text(criterio);
                $('#modalEstatus').removeClass().addClass('status-badge ' + getStatusClass(estatus)).text(estatus);

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

        // Función para inicializar DataTables de Vacantes
        function inicializarDataTableVacantes() {
            $('#tablaVacantes').DataTable({
                paging: false, // Deshabilitar paginación de DataTables porque usamos la nuestra
                searching: true,
                ordering: true,
                order: [[0, 'asc']],
                language: {
                    "search": "Buscar:",
                    "zeroRecords": "No se encontraron registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)"
                },
                dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '📊 Exportar a Excel',
                    className: 'btn btn-excel',
                    title: 'Vacantes_Activas_' + new Date().toISOString().slice(0, 10),
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4] // Exportar todas las columnas excepto Acciones
                    }
                }]
            });
        }

        // Función para inicializar DataTables de Candidatos
        function inicializarDataTableCandidatos() {
            $('#tablaCandidatos').DataTable({
                paging: false, // Deshabilitar paginación de DataTables porque usamos la nuestra
                searching: true,
                ordering: true,
                order: [[0, 'asc']],
                language: {
                    "search": "Buscar:",
                    "zeroRecords": "No se encontraron registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)"
                },
                dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '📊 Exportar a Excel',
                    className: 'btn btn-excel',
                    title: 'Candidatos_<?php echo $id_requisicion ? "Requisicion_" . $id_requisicion : "Todos"; ?>_' + new Date().toISOString().slice(0, 10)
                }]
            });
        }

        // Función para cambiar de pestaña
        function cambiarTab(tab, pagina = 1) {
            // Construir URL con parámetros actuales
            let url = `?tab=${tab}&pagina=${pagina}`;

            // Si estamos en la pestaña de candidatos y tenemos parámetros específicos
            if (tab === 'candidatos' && <?php echo !empty($titulo_vacante) ? 'true' : 'false'; ?>) {
                url += `&id_requisicion=<?php echo urlencode($id_requisicion); ?>&titulo=<?php echo urlencode($titulo_vacante); ?>`;
            }

            window.location.href = url;
        }
    </script>
</body>
</html>