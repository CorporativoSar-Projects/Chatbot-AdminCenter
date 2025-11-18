<?php
// Validación del usuario
include 'modelo/consultas_menu.php';

// URL de la API Django que devuelve JSON
$tokens_api_url = "http://localhost:8000/api/tokens/";

// Obtener datos desde la API
function obtenerDatosJSON($url)
{
    $json = @file_get_contents($url);
    if ($json === FALSE) {
        return [];
    }
    return json_decode($json, true);
}

// $tokens = obtenerDatosJSON($tokens_api_url);
$response = obtenerDatosJSON($tokens_api_url);
$tokens = $response['data'] ?? [];


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

    <title>Consumo de Tokens - Admin</title>
    <style>
        /* Estilos similares a vacantes */
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

        /* === Estilos nuevos para los valores restantes === */
        .badge-count,
        .badge-count-zero {
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 8px;
            font-size: 13px;
            margin-left: 4px;
            display: inline-block;
            min-width: 60px;
            text-align: center;
            font-family: 'Montserrat', sans-serif;
        }

        /* Restantes con tokens disponibles */
        .badge-count {
            color: #1b5e20;
            background-color: #e8f5e9;
            border: 1px solid #c8e6c9;
        }

        /* Restantes agotados o en cero */
        .badge-count-zero {
            color: #6c757d;
            background-color: #f1f3f4;
            border: 1px solid #dee2e6;
        }
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
                    <a href="https://billing.stripe.com/p/login/fZe3f33cggofeBy144" target="_blank">Actualizar Plan</a>
                    <a class="a1" href="cerrarSesion.php">Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="page-header">
            <h2>Consumo de Tokens de IA</h2>
            <div class="stats-info">
                <strong><?php echo count($tokens); ?> usuarios registrados</strong>
            </div>
        </div>

        <div class="table-container">
            <div class="table-responsive">
                <table id="tablaTokens" class="table table-striped table-bordered dt-responsive nowrap" width="100%">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Entrada / Restante</th>
                            <th>Salida / Restante</th>
                            <th>Memoria / Restante</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($tokens)): ?>
                            <?php foreach ($tokens as $t): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($t['user'] ?? ''); ?></td>

                                    <!-- Entrada / Restante Entrada -->
                                    <td>
                                        <?php echo number_format($t['input_tokens'] ?? 0); ?>
                                        /
                                        <span class="<?php echo (($t['remaining_input'] ?? 0) > 0) ? 'badge-count' : 'badge-count-zero'; ?>">
                                            <?php echo number_format($t['remaining_input'] ?? 0); ?>
                                        </span>
                                    </td>

                                    <!-- Salida / Restante Salida -->
                                    <td>
                                        <?php echo number_format($t['output_tokens'] ?? 0); ?>
                                        /
                                        <span class="<?php echo (($t['remaining_output'] ?? 0) > 0) ? 'badge-count' : 'badge-count-zero'; ?>">
                                            <?php echo number_format($t['remaining_output'] ?? 0); ?>
                                        </span>
                                    </td>

                                    <!-- Memoria / Restante Memoria -->
                                    <td>
                                        <?php echo number_format($t['memory_tokens'] ?? 0); ?>
                                        /
                                        <span class="<?php echo (($t['remaining_memory'] ?? 0) > 0) ? 'badge-count' : 'badge-count-zero'; ?>">
                                            <?php echo number_format($t['remaining_memory'] ?? 0); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="text-muted">
                                        <h5>No hay tokens disponibles</h5>
                                        <p>Verifica que los datos JSON o CSV estén correctamente cargados.</p>
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
            $('#tablaTokens').DataTable({
                paging: true,
                pageLength: 10,
                lengthChange: true,
                searching: true,
                ordering: true,
                order: [
                    [0, 'asc']
                ],
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '📊 Exportar a Excel',
                    className: 'btn btn-excel',
                    title: 'Consumo_Tokens_' + new Date().toISOString().slice(0, 10)
                }]
            });
        });
    </script>
</body>

</html>