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

// Conexión a la base de datos MySQL
$host = "127.0.0.1";
$user = "root";
$pass = "";
$dbname = "ixah";

$mysqli = new mysqli($host, $user, $pass, $dbname);
if ($mysqli->connect_errno) {
    die("Error al conectar a la base de datos: " . $mysqli->connect_error);
}

// Determinar qué pestaña está activa
$tab_activa = isset($_GET['tab']) ? $_GET['tab'] : 'candidatos';

// Configuración de paginación para CANDIDATOS
$registros_por_pagina_candidatos = 10;
$pagina_actual_candidatos = isset($_GET['pagina_candidatos']) ? max(1, intval($_GET['pagina_candidatos'])) : 1;
$offset_candidatos = ($pagina_actual_candidatos - 1) * $registros_por_pagina_candidatos;

// Consulta de CANDIDATOS con paginación
$query_count = "SELECT COUNT(*) as total FROM candidato";
$result_count = $mysqli->query($query_count);
$total_candidatos = $result_count->fetch_assoc()['total'];
$total_paginas_candidatos = ceil($total_candidatos / $registros_por_pagina_candidatos);

$query = "SELECT id_candidate, nombre_candidate, apellidop_candidate, correo_candidate, tel_candidate, CV_candidate, CV_id_onedrive FROM candidato LIMIT ?, ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ii", $offset_candidatos, $registros_por_pagina_candidatos);
$stmt->execute();
$result = $stmt->get_result();

$candidatos = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $candidatos[] = $row;
    }
}

// CARGAR VACANTES desde CSV (similar a la segunda página)
$csv_vacantes_url = "http://localhost/Chatbot-AdminCenter/puestos.csv";
$vacantes = [];

// Función para leer CSV
function leerCSVDesdeURL($url)
{
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

// Cargar vacantes
$vacantes = leerCSVDesdeURL($csv_vacantes_url);

// Si falla la carga de candidatos, usar datos de ejemplo
if (empty($candidatos)) {
    $candidatos = [
        [
            'id_candidate' => 1,
            'nombre_candidate' => 'ANA ANGELICA',
            'apellidop_candidate' => 'SOTO',
            'correo_candidate' => 'jdo@eeeisa.com.mx',
            'tel_candidate' => 'No especificado',
            'puesto' => 'GERENCIA DE VENTAS',
            'estado' => 'Examen médico'
        ],
        [
            'id_candidate' => 2,
            'nombre_candidate' => 'Luis Mauro',
            'apellidop_candidate' => 'Petro',
            'correo_candidate' => 'luiscarlos.vocacional5@gmail.com',
            'tel_candidate' => 'No especificado',
            'puesto' => 'GERENCIA DE VENTAS',
            'estado' => 'Contratado'
        ]
    ];
}

// Configuración de paginación para VACANTES
$registros_por_pagina_vacantes = 10;
$pagina_actual_vacantes = isset($_GET['pagina_vacantes']) ? max(1, intval($_GET['pagina_vacantes'])) : 1;
$total_vacantes = count($vacantes);
$total_paginas_vacantes = ceil($total_vacantes / $registros_por_pagina_vacantes);
$offset_vacantes = ($pagina_actual_vacantes - 1) * $registros_por_pagina_vacantes;
$vacantes_paginadas = array_slice($vacantes, $offset_vacantes, $registros_por_pagina_vacantes);

$mysqli->close();
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
    <title>Candidatos - Admin</title>
    <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css" />

    <style>
        /* TODOS TUS ESTILOS CSS SE MANTIENEN IGUAL CON ALGUNAS ADICIONES */
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

        /* Estilos para paginación */
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

        .btn-view {
            background: #002B45;
            color: white;
            border: none;
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0, 43, 69, 0.2);
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 43, 69, 0.3);
            color: white;
            text-decoration: none;
        }

        .candidate-id {
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 8px;
            font-weight: bold;
            color: #002B45;
        }

        .candidate-name {
            font-weight: 600;
            color: #002B45;
        }

        /* Botones especiales para análisis */
        .btn-select-candidate {
            background: #28a745 !important;
            color: white !important;
            border: none !important;
            border-radius: 25px !important;
            padding: 6px 15px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            margin-top: 5px !important;
        }

        .btn-select-candidate:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3) !important;
        }

        .btn-improve-job {
            background: #ff6b35 !important;
            color: white !important;
            border: none !important;
            border-radius: 25px !important;
            padding: 6px 15px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            margin-top: 5px !important;
        }

        .btn-improve-job:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 3px 10px rgba(255, 107, 53, 0.3) !important;
        }

        .btn-compare-candidate {
            background: #17a2b8 !important;
            color: white !important;
            border: none !important;
            border-radius: 25px !important;
            padding: 6px 15px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
            margin-top: 5px !important;
        }

        .btn-compare-candidate:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 3px 10px rgba(23, 162, 184, 0.3) !important;
        }

        /* ESTILOS DEL CHAT (se mantienen igual) */
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

        #chat-body::-webkit-scrollbar {
            width: 6px;
        }

        #chat-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #chat-body::-webkit-scrollbar-thumb {
            background: #002B45;
            border-radius: 10px;
        }

        #chat-body::-webkit-scrollbar-thumb:hover {
            background: #002B45;
        }

        .stats-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 15px;
            border-radius: 10px;
            margin-top: 10px;
            font-size: 14px;
        }

        /* BOTON */
        /* Estilos mejorados para el botón toggle del chat */
        .chat-toggle-btn {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: white;
            font-family: "Montserrat", sans-serif;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.3px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .chat-toggle-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateX(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .chat-toggle-btn:active {
            transform: translateX(-1px);
            transition: all 0.1s ease;
        }

        .toggle-icon {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            stroke: white;
        }

        .toggle-text {
            opacity: 0.9;
            transition: opacity 0.3s ease;
        }

        .chat-toggle-btn:hover .toggle-text {
            opacity: 1;
        }

        /* Estado cuando el chat está cerrado */
        #chat-panel.hidden+#content .chat-toggle-btn {
            border-radius: 50%;
            padding: 10px;
            width: 40px;
            height: 40px;
        }

        #chat-panel.hidden+#content .toggle-text {
            display: none;
        }

        #chat-panel.hidden+#content .toggle-icon {
            transform: rotate(180deg);
        }

        /* Para el botón flotante cuando el chat está cerrado */
        #chat-open-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #002B45, #3ca6e5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(0, 43, 69, 0.4);
            z-index: 9999;
            border: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        #chat-open-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), transparent);
            border-radius: 50%;
        }

        #chat-open-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 25px rgba(0, 43, 69, 0.6);
        }

        #chat-open-btn:active {
            transform: translateY(-1px) scale(0.98);
        }

        #chat-open-btn img {
            width: 24px;
            filter: brightness(0) invert(1);
            position: relative;
            z-index: 1;
            transition: transform 0.3s ease;
        }

        #chat-open-btn:hover img {
            transform: scale(1.1);
        }

        /* Efecto de pulso opcional para llamar atención */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(60, 166, 229, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(60, 166, 229, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(60, 166, 229, 0);
            }
        }

        #chat-open-btn.pulse {
            animation: pulse 2s infinite;
        }

        /* Responsive para el botón toggle */
        @media (max-width: 768px) {
            .chat-toggle-btn {
                padding: 6px 12px;
                font-size: 11px;
            }

            .toggle-icon {
                width: 16px;
                height: 16px;
            }

            #chat-open-btn {
                width: 50px;
                height: 50px;
                bottom: 20px;
                right: 20px;
            }

            #chat-open-btn img {
                width: 20px;
            }
        }

        /* Versión alternativa más minimalista */
        .chat-toggle-btn.minimal {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 6px 12px;
        }

        .chat-toggle-btn.minimal:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Versión con solo ícono (para pantallas pequeñas) */
        .chat-toggle-btn.icon-only {
            padding: 8px;
            min-width: 36px;
            min-height: 36px;
        }

        .chat-toggle-btn.icon-only .toggle-text {
            display: none;
        }
    </style>
</head>

<body>
    <div id="chat-open-btn" style="display:none;">
        <img src="https://img.icons8.com/ios-filled/24/ffffff/chat.png" alt="Abrir Chat">
    </div>

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

    <div id="main-container">
        <div id="content">
            <!-- Pestañas -->
            <div class="tabs-container">
                <button class="tab-btn <?php echo $tab_activa == 'candidatos' ? 'active' : ''; ?>"
                    onclick="cambiarTab('candidatos')">
                    👥 Candidatos
                </button>
                <button class="tab-btn <?php echo $tab_activa == 'vacantes' ? 'active' : ''; ?>"
                    onclick="cambiarTab('vacantes')">
                    📋 Vacantes
                </button>
            </div>

            <!-- Tab de CANDIDATOS -->
            <div id="candidatos-content" class="tab-content <?php echo $tab_activa == 'candidatos' ? 'active' : ''; ?>">
                <!-- Header de la página -->
                <div class="page-header">
                    <h2>Lista de Candidatos</h2>
                    <div class="stats-info">
                        <strong>Total: <?php echo $total_candidatos; ?> candidatos</strong> |
                        Página <?php echo $pagina_actual_candidatos; ?> de <?php echo $total_paginas_candidatos; ?>
                    </div>
                </div>

                <!-- Contenedor de la tabla de candidatos -->
                <div class="table-container">
                    <table id="tablaCandidatos" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Teléfono</th>
                                <th>CV</th>
                                <th>Acciones IA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($candidatos) > 0): ?>
                                <?php foreach ($candidatos as $candidato): ?>
                                    <tr>
                                        <td>
                                            <span class="candidate-id">
                                                #<?php echo str_pad($candidato['id_candidate'], 3, '0', STR_PAD_LEFT); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="candidate-name">
                                                <?php echo htmlspecialchars($candidato['nombre_candidate'] . ' ' . $candidato['apellidop_candidate']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($candidato['tel_candidate']); ?></td>
                                        <td>
                                            <?php if (!empty($candidato['CV_candidate'])): ?>
                                                <a href="<?php echo htmlspecialchars($candidato['CV_candidate']); ?>" target="_blank" class="btn btn-sm btn-info">
                                                    📄 Ver CV
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">No disponible</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-select-candidate"
                                                onclick="seleccionarParaAnalisis(<?php echo $candidato['id_candidate']; ?>)"
                                                style="display: none;">
                                                ✅ Seleccionar para Análisis
                                            </button>
                                            <button class="btn btn-improve-job"
                                                onclick="seleccionarParaMejoraPuesto(<?php echo $candidato['id_candidate']; ?>)"
                                                style="display: none;">
                                                ✏️ Mejorar Descripción
                                            </button>
                                            <button class="btn btn-compare-candidate"
                                                onclick="compararCVConSAP(<?php echo $candidato['id_candidate']; ?>)"
                                                style="display: none;">
                                                🔍 Comparar CV con SAP
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <h5>No hay candidatos registrados</h5>
                                            <p>Los candidatos aparecerán aquí cuando se registren.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación para Candidatos -->
                <?php if ($total_paginas_candidatos > 1): ?>
                    <div class="pagination-container">
                        <ul class="pagination-custom">
                            <!-- Botón Anterior -->
                            <?php if ($pagina_actual_candidatos > 1): ?>
                                <li class="page-item-custom">
                                    <a class="page-link-custom" href="?pagina_candidatos=<?php echo $pagina_actual_candidatos - 1; ?>&tab=<?php echo $tab_activa; ?>">
                                        &laquo; Anterior
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item-custom">
                                    <span class="page-link-custom disabled">&laquo; Anterior</span>
                                </li>
                            <?php endif; ?>

                            <!-- Números de página -->
                            <?php for ($i = 1; $i <= $total_paginas_candidatos; $i++): ?>
                                <li class="page-item-custom <?php echo $i == $pagina_actual_candidatos ? 'active' : ''; ?>">
                                    <a class="page-link-custom" href="?pagina_candidatos=<?php echo $i; ?>&tab=<?php echo $tab_activa; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <!-- Botón Siguiente -->
                            <?php if ($pagina_actual_candidatos < $total_paginas_candidatos): ?>
                                <li class="page-item-custom">
                                    <a class="page-link-custom" href="?pagina_candidatos=<?php echo $pagina_actual_candidatos + 1; ?>&tab=<?php echo $tab_activa; ?>">
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

            <!-- Tab de VACANTES -->
            <div id="vacantes-content" class="tab-content <?php echo $tab_activa == 'vacantes' ? 'active' : ''; ?>">
                <!-- Header de vacantes -->
                <div class="page-header">
                    <h2>📊 Vacantes Activas</h2>
                    <div class="stats-info">
                        <strong>Total: <?php echo $total_vacantes; ?> vacantes</strong> |
                        Página <?php echo $pagina_actual_vacantes; ?> de <?php echo $total_paginas_vacantes; ?>
                    </div>
                </div>

                <!-- Contenedor de la tabla de vacantes -->
                <div class="table-container">
                    <table id="tablaVacantes" class="table table-striped table-bordered">
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
                            <?php if (!empty($vacantes_paginadas)): ?>
                                <?php foreach ($vacantes_paginadas as $vacante):
                                    $idRequisicion = $vacante['reqId_ix'] ?? $vacante['ID de requisición de personal'] ?? '';
                                    $titulo = $vacante['title_ix'] ?? $vacante['Titulo'] ?? '';
                                    $categoria = $vacante['category_ix'] ?? $vacante['Categoría'] ?? '';
                                    $ubicacion = $vacante['location_ix'] ?? $vacante['Ubicación'] ?? '';
                                    $link = $vacante['link'] ?? '#';
                                ?>
                                    <tr>
                                        <td>
                                            <span class="requisicion-id">#<?php echo htmlspecialchars($idRequisicion); ?></span>
                                        </td>
                                        <td>
                                            <div class="puesto-title"><?php echo htmlspecialchars($titulo); ?></div>
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

                                                <!-- EL BOTÓN APUNTA A OTRA PÁGINA, NO A UNA PESTAÑA -->
                                                <a href="detalle_candidatos_vacante.php?id_requisicion=<?php echo urlencode($idRequisicion); ?>&titulo=<?php echo urlencode($titulo); ?>" class="btn btn-candidates">
                                                    👥 Ver Candidatos
                                                </a>
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

                <!-- Paginación para Vacantes -->
                <?php if ($total_paginas_vacantes > 1): ?>
                    <div class="pagination-container">
                        <ul class="pagination-custom">
                            <!-- Botón Anterior -->
                            <?php if ($pagina_actual_vacantes > 1): ?>
                                <li class="page-item-custom">
                                    <a class="page-link-custom" href="?pagina_vacantes=<?php echo $pagina_actual_vacantes - 1; ?>&tab=<?php echo $tab_activa; ?>">
                                        &laquo; Anterior
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="page-item-custom">
                                    <span class="page-link-custom disabled">&laquo; Anterior</span>
                                </li>
                            <?php endif; ?>

                            <!-- Números de página -->
                            <?php for ($i = 1; $i <= $total_paginas_vacantes; $i++): ?>
                                <li class="page-item-custom <?php echo $i == $pagina_actual_vacantes ? 'active' : ''; ?>">
                                    <a class="page-link-custom" href="?pagina_vacantes=<?php echo $i; ?>&tab=<?php echo $tab_activa; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <!-- Botón Siguiente -->
                            <?php if ($pagina_actual_vacantes < $total_paginas_vacantes): ?>
                                <li class="page-item-custom">
                                    <a class="page-link-custom" href="?pagina_vacantes=<?php echo $pagina_actual_vacantes + 1; ?>&tab=<?php echo $tab_activa; ?>">
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
        </div>

        <!-- PANEL DEL CHAT (se mantiene igual) -->
        <div id="chat-panel">
            <div id="chat-header">
                <span>Asistente IA</span>
                <button id="toggle-chat" class="chat-toggle-btn">
                    <!--<svg id="toggle-icon" class="toggle-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 18l-6-6 6-6" />
                    </svg>-->
                    <span class="toggle-text">Ocultar Chat</span>
                </button>
            </div>
            <div id="chat-body">
                <div class="chat-message bot-message">
                    ¡Hola! Soy tu asistente de IA. ¿En qué puedo ayudarte hoy?
                    <div class="special-buttons">
                        <button class="special-btn" onclick="activarComparacionCV()">
                            🔍 Análisis de candidatos
                        </button>
                        <button class="special-btn" onclick="mostrarOpcionesMejoraDescripcion()">
                            ✏️ Mejorar descripción de puesto
                        </button>
                        <div id="opciones-mejora-container" class="analysis-input-container" style="display: none;">
                            <div style="text-align: center; margin-bottom: 15px;">
                                <h5 style="color: #002B45; margin-bottom: 20px;">Selecciona una opción:</h5>
                                <button class="special-btn" onclick="seleccionarDescripcionManual()"
                                    style="margin-bottom: 10px; background: #3ca6e5;">
                                    📝 Descripción Manual
                                </button>
                                <button class="special-btn" onclick="seleccionarDescripcionATS()"
                                    style="margin-bottom: 10px; background: #28a745;">
                                    📊 Descripción desde ATS
                                </button>
                                <button class="special-btn" onclick="ocultarOpcionesMejora()"
                                    style="background: #6c757d; color: white;">
                                    ↩️ Volver
                                </button>
                            </div>
                        </div>
                        <!--
                        <button class="special-btn" onclick="procesarSAPSSFF()">
                            📊 Procesar SAP SSFF
                        </button>-->
                    </div>
                </div>
                <div id="manual-description-container" class="analysis-input-container" style="display: none;">
                    <textarea id="descripcion-puesto-input" class="analysis-input" rows="4" placeholder="Escribe aquí la descripción del puesto que deseas mejorar..."></textarea>
                    <button class="analysis-btn" onclick="enviarDescripcionParaMejora()">
                        ✏️ Optimizar descripción con IA
                    </button>
                </div>
                <div id="analysis-input-container" class="analysis-input-container">
                    <input type="text" id="candidate-name-input" class="analysis-input"
                        placeholder="Escribe el nombre del candidato a analizar...">
                    <button class="analysis-btn" onclick="analizarCandidato()">
                        🔍 Analizar Candidato
                    </button>
                </div>
            </div>
            <div id="chat-footer">
                <input type="text" id="chat-input" placeholder="Consulta sobre candidatos, vacantes...">
                <button id="send-btn">Enviar</button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>

    <!-- Scripts de IA -->
    <script>

        // Datos de candidatos desde PHP
        const candidatosData = <?php echo json_encode($candidatos); ?>;
        const vacantesData = <?php echo json_encode($vacantes); ?>;

        $(document).ready(function() {
            // Inicializar DataTable para CANDIDATOS
            $('#tablaCandidatos').DataTable({
                paging: false, // Deshabilitar paginación de DataTables porque usamos la nuestra
                searching: true,
                ordering: true,
                order: [
                    [0, 'desc']
                ],
                language: {
                    "search": "Buscar:",
                    "zeroRecords": "No se encontraron registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)"
                }
            });

            // Inicializar DataTable para VACANTES
            $('#tablaVacantes').DataTable({
                paging: false, // Deshabilitar paginación de DataTables porque usamos la nuestra
                searching: true,
                ordering: true,
                order: [
                    [0, 'asc']
                ],
                language: {
                    "search": "Buscar:",
                    "zeroRecords": "No se encontraron registros",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(filtrado de _MAX_ registros totales)"
                }
            });

            // Funcionalidad del chat (se mantiene igual)
            const toggleBtn = document.getElementById('toggle-chat');
            const chatPanel = document.getElementById('chat-panel');
            const content = document.getElementById('content');
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
        });

        // Función para cambiar de pestaña
        function cambiarTab(tab) {
            // Construir URL con la pestaña seleccionada
            let url = `?tab=${tab}`;

            // Agregar parámetro de paginación según la pestaña
            if (tab === 'candidatos') {
                url += `&pagina_candidatos=1`;
            } else if (tab === 'vacantes') {
                url += `&pagina_vacantes=1`;
            }

            window.location.href = url;
        }

        // Funciones del chat (se mantienen igual)
        function mostrarOpcionesMejoraDescripcion() {
            document.getElementById('analysis-input-container').style.display = 'none';
            document.getElementById('manual-description-container').style.display = 'none';
            document.getElementById('opciones-mejora-container').style.display = 'block';
            agregarMensajeChat('user', 'Quiero mejorar una descripción de puesto');
            setTimeout(() => {
                agregarMensajeChat('bot', 'Por favor, selecciona el tipo de descripción que deseas mejorar:');
            }, 500);
        }

        function seleccionarDescripcionManual() {
            document.getElementById('opciones-mejora-container').style.display = 'none';
            agregarMensajeChat('user', 'Descripción Manual');
            setTimeout(() => {
                document.getElementById('manual-description-container').style.display = 'block';
            }, 500);
        }

        function seleccionarDescripcionATS() {
            document.getElementById('opciones-mejora-container').style.display = 'none';
            agregarMensajeChat('user', 'Descripción desde ATS');
            setTimeout(() => {
                agregarMensajeChat('bot', 'Selecciona un candidato de la tabla para mejorar la descripción de su puesto.');
                mostrarBotonesMejoraEnTabla();
            }, 500);
        }

        function mostrarBotonesMejoraEnTabla() {
            ocultarTodosLosBotones();
            document.querySelectorAll('.btn-improve-job').forEach(btn => {
                btn.style.display = 'inline-block';
            });
        }

        function ocultarTodosLosBotones() {
            document.querySelectorAll('.btn-select-candidate').forEach(btn => {
                btn.style.display = 'none';
            });
            document.querySelectorAll('.btn-improve-job').forEach(btn => {
                btn.style.display = 'none';
            });
            document.querySelectorAll('.btn-compare-candidate').forEach(btn => {
                btn.style.display = 'none';
            });
        }

        function agregarMensajeChat(tipo, mensaje) {
            const chatBody = document.getElementById('chat-body');
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${tipo}-message`;
            messageDiv.textContent = mensaje;
            chatBody.appendChild(messageDiv);
            chatBody.scrollTop = chatBody.scrollHeight;
        }
    </script>
    <script src="js/IA/analisisIA.js"></script>
</body>

</html>