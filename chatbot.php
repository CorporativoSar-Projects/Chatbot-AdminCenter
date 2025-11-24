<?php
/*include 'modelo/consultas_menu.php';

// Si la sesión no está iniciada, entonces iníciala
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_adm'])) {
    header("location: ./index.php?error=2");
    exit;
}

// Cargar datos desde CSV en lugar de MySQL
$candidatos = [];
$csv_url = "http://localhost/Chatbot-AdminCenter/candidatos.csv";

// Función para cargar datos del CSV
function cargarCandidatosDesdeCSV($url)
{
    $candidatos = [];

    if (($handle = fopen($url, "r")) !== FALSE) {
        $headers = fgetcsv($handle, 1000, ",");

        // Mapear headers en español a nombres de campos más legibles
        $header_map = [
            'Titulo' => 'puesto',
            'Estado' => 'estado',
            'Nombre' => 'nombre_candidate',
            'Apellido' => 'apellidop_candidate',
            'Correo ElectrÃ³nico' => 'correo_candidate'
        ];

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) >= 5) { // Asegurar que tenemos al menos los campos básicos
                $candidato = [];
                foreach ($headers as $index => $header) {
                    $clean_header = trim($header);
                    if (isset($header_map[$clean_header]) && isset($data[$index])) {
                        $candidato[$header_map[$clean_header]] = utf8_encode($data[$index]);
                    }
                }

                // Agregar campos adicionales para compatibilidad
                $candidato['id_candidate'] = count($candidatos) + 1;
                $candidato['tel_candidate'] = 'No especificado';

                if (!empty($candidato)) {
                    $candidatos[] = $candidato;
                }
            }
        }
        fclose($handle);
    }

    return $candidatos;
}

// Cargar candidatos desde CSV
$candidatos = cargarCandidatosDesdeCSV($csv_url);

// Si falla la carga del CSV, usar datos de ejemplo
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
}*/

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

// Consulta todos los candidatos
$query = "SELECT id_candidate, nombre_candidate, apellidop_candidate, correo_candidate, tel_candidate, CV_candidate, CV_id_onedrive FROM candidato";
$result = $mysqli->query($query);

$candidatos = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $candidatos[] = $row;
    }
}
/*
'id_candidate' => $row['id_candidate'],
            'nombre_candidate' => $row['nombre_candidate'],
            'apellidop_candidate' => $row['apellidop_candidate'],
            'correo_candidate' => $row['correo_candidate'],
            'tel_candidate' => $row['tel_candidate'] ?? 'No especificado',
            'puesto' => 'Sin especificar',  // Opcional, puedes mapearlo si hay campo
            'estado' => 'Pendiente'         // Opcional, puedes mapearlo si hay campo
*/

// Si falla la carga, puedes usar los datos de ejemplo como fallback
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

    <style>
        /* TODOS TUS ESTILOS CSS SE MANTIENEN IGUAL */
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
            background: #002B45;
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

        .badge {
            color: black !important;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 12px;
        }

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
                        <a href="vacantes.php">Vacantes</a>
                    </div>
                    <div class="user-info">
                        <a href="candidatos.php">Candidatos</a>
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
            <!-- Header de la página -->
            <div class="page-header">
                <h2>Lista de Candidatos</h2>
                <small class="mt-2 d-block">Datos cargados desde archivo CSV</small>
            </div>

            <!-- Contenedor de la tabla -->
            <div class="table-container">
                <table id="tablaCandidatos" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <!--<th>Email</th>-->
                            <th>Teléfono</th>
                            <th>CV</th>
                            <!--<th>Estado</th>-->
                            <th>Acciones</th>
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
                                    <!--
                                    <td>
                                        <span class="badge badge-primary">
                                            <?php /*echo isset($candidato['puesto']) ? htmlspecialchars($candidato['puesto']) : 'Sin especificar'; */ ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php /*
                                                                    echo isset($candidato['estado']) && $candidato['estado'] === 'Contratado' ? 'success' : 'warning';
                                                                    */ ?>">
                                            <?php /* echo isset($candidato['estado']) ? htmlspecialchars($candidato['estado']) : 'Pendiente'; */ ?>
                                        </span>
                                    </td>-->
                                    <td>
                                        <a href="<?php echo htmlspecialchars($candidato['CV_candidate']); ?>" target="_blank" class="btn btn-sm btn-info">
                                            📄 Ver CV
                                        </a>
                                    </td>
                                    <td>
                                        <!--<a href="#" class="btn btn-view" onclick="verDetalle(<?php echo $candidato['id_candidate']; ?>)">
                                            Ver Detalle
                                        </a>-->
                                        <button class="btn btn-select-candidate"
                                            onclick="seleccionarParaAnalisis(<?php echo $candidato['id_candidate']; ?>)"
                                            style="display: none; margin-top: 5px; background: #28a745; color: white; border: none; border-radius: 25px; padding: 6px 15px; font-size: 12px;">
                                            ✅ Seleccionar para Análisis
                                        </button>
                                        <button class="btn btn-improve-job"
                                            onclick="seleccionarParaMejoraPuesto(<?php echo $candidato['id_candidate']; ?>)"
                                            style="display: none; margin-top: 5px; background: #ff6b35; color: white; border: none; border-radius: 25px; padding: 6px 15px; font-size: 12px;">
                                            ✏️ Mejorar Descripción
                                        </button>
                                        <button class="btn btn-compare-candidate"
                                            onclick="compararCVConSAP(<?php echo $candidato['id_candidate']; ?>)"
                                            style="display: none; margin-top: 5px; background: #17a2b8; color: white; border: none; border-radius: 25px; padding: 6px 15px; font-size: 12px;">
                                            🔍 Comparar CV con SAP
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
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
        </div>

        <div id="chat-panel">
            <div id="chat-header">
                <span>Asistente IA</span>
                <button id="toggle-chat">
                    <img id="toggle-icon" src="https://img.icons8.com/ios-filled/24/ffffff/chevron-left.png" alt="Toggle">
                </button>
            </div>
            <div id="chat-body">
                <!-- Mensaje de bienvenida -->
                <div class="chat-message bot-message">
                    ¡Hola! Soy tu asistente de IA. ¿En qué puedo ayudarte hoy?
                    <!-- Botones especializados -->
                    <div class="special-buttons">
                        <button class="special-btn" onclick="mostrarAnalisisCandidato()">
                            🔍 Análisis de candidatos
                        </button>
                        <button class="special-btn" onclick="mejorarDescripcionPuesto()">
                            ✏️ Mejorar descripciones de puestos
                        </button>
                        <button class="special-btn" onclick="mostrarInputManualDescripcion()">
                            ✏️ Mejorar descripción manual
                        </button>
                        <button class="special-btn" onclick="procesarSAPSSFF()">
                            📊 Procesar SAP SSFF
                        </button>
                        <button class="special-btn" onclick="activarComparacionCV()">
                            🔍 Comparar CV con SAP
                        </button>
                    </div>
                </div>
                <div id="manual-description-container" class="analysis-input-container">
                    <textarea id="descripcion-puesto-input" class="analysis-input" rows="4" placeholder="Escribe aquí la descripción del puesto que deseas mejorar..."></textarea>
                    <button class="analysis-btn" onclick="enviarDescripcionParaMejora()">
                        ✏️ Optimizar descripción con IA
                    </button>
                </div>

                <!-- Contenedor para análisis de candidatos -->
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

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

    <!-- Scripts de IA -->
    <script>
        // Datos de candidatos desde PHP
        const candidatosData = <?php echo json_encode($candidatos); ?>;
    </script>
    <script src="js/IA/analisisIA.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializar DataTable
            $('#tablaCandidatos').DataTable({
                paging: true,
                pageLength: 10,
                lengthChange: true,
                searching: true,
                ordering: true,
                order: [
                    [0, 'desc']
                ],
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

            // SOLO FUNCIONALIDAD DEL TOGGLE DEL CHAT
            const toggleBtn = document.getElementById('toggle-chat');
            const chatPanel = document.getElementById('chat-panel');
            const content = document.getElementById('content');
            const chatOpenBtn = document.getElementById('chat-open-btn');

            // Toggle del chat
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

        // Función básica para ver detalle
        function verDetalle(id) {
            const candidato = candidatosData.find(c => c.id_candidate == id);
            if (candidato) {
                Swal.fire({
                    title: `Detalle de ${candidato.nombre_candidate} ${candidato.apellidop_candidate}`,
                    html: `
                    <div class="text-left">
                        <p><strong>Email:</strong> ${candidato.correo_candidate}</p>
                        <p><strong>Teléfono:</strong> ${candidato.tel_candidate}</p>
                        <p><strong>Puesto:</strong> ${candidato.puesto || 'No especificado'}</p>
                        <p><strong>Estado:</strong> ${candidato.estado || 'Pendiente'}</p>
                    </div>
                `,
                    icon: 'info',
                    confirmButtonText: 'Cerrar'
                });
            }
        }
    </script>
</body>

</html>