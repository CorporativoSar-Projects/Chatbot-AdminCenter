<?php
include 'modelo/consultas_menu.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/sty.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap"
        rel="stylesheet" />
    <title>Home - IXAH</title>
    <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --background-color: rgb(0, 43, 69);
            --accent-color: #9b59b6;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
            --card-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            --border-radius: 12px;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);*/
            background: --light-bg;
            min-height: 100vh;
        }

        main#contenidoPrincipal {
            margin-top: 30px;
            padding-bottom: 50px;
        }

        .container-prin {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header mejorado */
        .container-bienv {
            text-align: center;
            margin-bottom: 30px;
            padding: 40px 20px;
            background: var(--background-color);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: space-between;
            flex-direction: column;
            align-items: center;
        }

        .txtBien {
            font-size: 3rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
            background-color: #e9ecef;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .txt-disena-chat {
            text-align: center;
            margin-bottom: 40px;
        }

        .txt-disena-chat p {
            font-size: 1.5rem;
            color: black;
            font-weight: 600;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            margin: 0;
        }

        /* Cards modernas */
        .container-sap {
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 30px;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .container-sap:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .container-sap h5 {
            color: var(--primary-color);
            font-weight: 700;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 10px;
            font-size: 1.3rem;
        }

        /* Formularios mejorados */
        .container-sap textarea,
        .container-sap input[type="text"],
        .container-sap input[type="file"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .container-sap textarea:focus,
        .container-sap input[type="text"]:focus,
        .container-sap input[type="file"]:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
            outline: none;
        }

        .container-sap textarea {
            min-height: 120px;
            resize: vertical;
            font-family: 'Courier New', monospace;
            background: #f8f9fa;
        }

        /* Botones mejorados */
        .container-sap button {
            background: linear-gradient(135deg, var(--secondary-color), #2980b9);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .container-sap button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(52, 152, 219, 0.4);
        }

        .container-sap button:active {
            transform: translateY(0);
        }

        /* Tabla mejorada */
        #tablaPuestos {
            width: 100%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        #tablaPuestos thead {
            background: linear-gradient(135deg, var(--primary-color), #34495e);
            color: white;
        }

        #tablaPuestos th {
            padding: 15px 12px;
            font-weight: 600;
            text-align: left;
            font-size: 0.9rem;
        }

        #tablaPuestos td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 0.85rem;
        }

        #tablaPuestos tbody tr {
            transition: background-color 0.3s ease;
        }

        #tablaPuestos tbody tr:hover {
            background-color: #f8f9fa;
        }

        #tablaPuestos tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        #tablaPuestos tbody tr:nth-child(even):hover {
            background-color: #e9ecef;
        }

        /* Modal mejorado */
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color), #34495e);
            color: white;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            padding: 20px 25px;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.3rem;
        }

        .modal-body {
            padding: 25px;
        }

        .custom-input {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .custom-input:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .submit-button-form {
            background: linear-gradient(135deg, var(--success-color), #229954);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .submit-button-form:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(39, 174, 96, 0.4);
        }

        /* Grid responsivo */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--border-radius);
            padding: 25px;
            text-align: center;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .feature-card h4 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .feature-card p {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Estados de carga */
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
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container-prin {
                padding: 0 15px;
            }

            .txtBien {
                font-size: 2rem;
            }

            .txt-disena-chat p {
                font-size: 1.2rem;
            }

            .container-sap {
                padding: 20px;
            }

            #tablaPuestos {
                font-size: 0.8rem;
            }

            #tablaPuestos th,
            #tablaPuestos td {
                padding: 8px 6px;
            }
        }

        /* Animaciones */
        .fade-in {
            animation: fadeIn 0.6s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /*.pulse {
            animation: pulse 2s infinite;
        }*/

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
</head>

<body>
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
                <!-- Menú actual -->
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
                        <a href="vacantes.php">Vacantes</a>
                    </div>
                    <div class="user-info">
                        <a href="crear_perfil_manual.php">Informe Manual</a>
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
                            🔗 Integración SFTP
                        </a>
                        <a href="https://billing.stripe.com/p/login/fZe3f33cggofeBy144" target="_blank">💳 Actualizar Plan</a>
                    </div>
                    <a class="a1" href="cerrarSesion.php">🚪 Cerrar Sesión</a>
                </div>
            </div>
        </div>
    </header>

    <main id="contenidoPrincipal" class="fade-in">
        <div class="container-prin">
            <!-- Header de bienvenida -->
            <div class="container-bienv pulse">
                <p class="txtBien">Bienvenido, <?php echo $_SESSION['nombre_adm']; ?>!</p>
                <p style="color: #666; font-size: 1.1rem; margin-top: 10px;">Panel de Gestión SAP - IXAH</p>
            </div>

            <div class="txt-disena-chat">
                <p>🚀 Gestión Integral de Puestos desde SAP</p>
            </div>

            <!-- Grid de características -->
            <div class="features-grid">
                <div class="feature-card">
                    <a href="crear_perfil_manual.php">
                        <div class="feature-icon">📁</div>
                        <h4>Diseño manual</h4>
                        <p>Diseña manualmente la vacante de puesto</p>
                    </a>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h4>Importación JSON</h4>
                    <p>Importa puestos individuales mediante JSON para una integración rápida y precisa</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📋</div>
                    <h4>Gestión de Puestos</h4>
                    <p>Visualiza y administra todos los puestos almacenados en el sistema</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">📁</div>
                    <h4>Importación CSV</h4>
                    <p>Carga múltiples puestos simultáneamente desde archivos CSV</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🌐</div>
                    <h4>Importación URLs</h4>
                    <p>Importa automáticamente desde URLs configuradas previamente</p>
                </div>
            </div>
            <br><br><br>

            <!-- Sección: Importar un puesto vía JSON (API) -->
            <div class="container-sap">
                <h5>📥 Importar puesto individual (JSON)</h5>
                <form id="formImportarJson">
                    <textarea name="jsonData" placeholder='{
  "requisition_id": "REQ001",
  "category": "Tecnología",
  "title": "Desarrollador Full Stack",
  "link": "https://ejemplo.com/puesto",
  "location": "Ciudad de México",
  "description": "Descripción del puesto..."
}'></textarea>
                    <button type="button" id="btnImportarJson">
                        <span class="spinner" style="display: none;"></span>
                        📤 Importar JSON
                    </button>
                </form>
            </div>

            <!-- Sección: Listar puestos actuales -->
            <div class="container-sap">
                <h5>📋 Puestos almacenados</h5>
                <button type="button" id="btnListarPuestos">
                    <span class="spinner" style="display: none;"></span>
                    🔄 Actualizar listado
                </button>
                <div style="overflow-x: auto;">
                    <table id="tablaPuestos">
                        <thead>
                            <tr>
                                <th>ID Requisición</th>
                                <th>Categoría</th>
                                <th>Título</th>
                                <th>Link</th>
                                <th>Ubicación</th>
                                <th>Fecha Importación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 30px; color: #666;">
                                    <div class="spinner mx-auto"></div>
                                    <p style="margin-top: 10px;">Cargando puestos...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sección: Importar CSV -->
            <div class="container-sap">
                <h5>📁 Importar puestos desde CSV</h5>
                <form id="formImportarCsv" enctype="multipart/form-data">
                    <input type="file" name="file" accept=".csv" style="margin-bottom: 15px;">
                    <button type="button" id="btnImportarCsv">
                        <span class="spinner" style="display: none;"></span>
                        📤 Importar CSV
                    </button>
                    <small style="display: block; color: #666; margin-top: 10px;">
                        Formato esperado: requisition_id,category,title,link,location
                    </small>
                </form>
            </div>

            <!-- Sección: Importar desde URLs configuradas -->
            <div class="container-sap">
                <h5>🌐 Importar puestos desde URLs configuradas</h5>
                <button type="button" id="btnImportarUrls">
                    <span class="spinner" style="display: none;"></span>
                    🔗 Importar desde URLs
                </button>
                <small style="display: block; color: #666; margin-top: 10px;">
                    Importa automáticamente desde todas las URLs configuradas en el sistema
                </small>
            </div>

        </div>
    </main>

    <!-- Modal Integración SFTP (mantener estructura actual) -->
    <div class="modal fade" id="sftpModal" tabindex="-1" role="dialog" aria-labelledby="sftpModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sftpModalLabel">🔗 Configuración de Integración SFTP</h5>
                    <span class="cerrar-modal" id="cerrarIntegracion" data-dismiss="modal">&times;</span>
                </div>
                <div class="modal-body" style="margin-left: 40px">
                    <form id="formIntegracionSFTP">
                        <div class="d-flex align-items-center mb-3">
                            <input class="form-check-input small-checkbox me-2" type="checkbox" id="sftpCheckbox"
                                <?php echo ($sftpActivo == 1) ? 'checked' : ''; ?>>
                            <label for="sftpCheckbox" class="m-0">Activar integración SFTP</label>
                        </div>
                        <label>🖥️ Servidor:</label>
                        <input type="text" class="form-control custom-input" name="servidor" required />
                        <label>🔌 Puerto:</label>
                        <input type="text" class="form-control custom-input" name="puerto" value="22" readonly />
                        <label>👤 Usuario:</label>
                        <input type="text" class="form-control custom-input" name="usuario" required />
                        <label>🔒 Contraseña:</label>
                        <input type="password" class="form-control custom-input" name="contrasena" />
                        <label>📁 Ruta de Destino:</label>
                        <input type="text" class="form-control custom-input" name="rutaDestino" required />
                    </form>
                </div>
                <div class="modal-footer1">
                    <button class="submit-button-form" type="submit" form="formIntegracionSFTP">
                        💾 Guardar Configuración
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales existentes (mantener estructura) -->
    <div class="modal fade" id="modalAvisoCancelacion" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">⚠️ Cancelación en Proceso</h5>
                </div>
                <div class="modal-body">
                    <p>Tu suscripción será cancelada al finalizar el periodo actual. Aún puedes usar el sistema hasta esa fecha.</p>
                </div>
                <div class="modal-footer1">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSuspension" tabindex="-1" role="dialog" aria-labelledby="modalSuspensionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalSuspensionLabel">❌ Suscripción inactiva</h5>
                </div>
                <div class="modal-body">
                    <p>Tu suscripción ha sido cancelada o pausada. No puedes usar el sistema hasta contratar una nueva suscripción.</p>
                    <form id="nuevoPlanForm">
                        <div class="form-group">
                            <label for="planSelect">Selecciona un plan:</label>
                            <select id="planSelect" class="form-control" required>
                                <option value="">-- Elige un plan --</option>
                                <option value="basico3m">Mensual</option>
                                <option value="planAnual">Anual</option>
                            </select>
                        </div>
                        <button type="button" id="btnContratarPlan" class="btn btn-primary mt-2">Contratar Plan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/sap_import.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/loginError.js" type="module"></script>
    <script src="js/menuLateral.js" type="module"></script>
    <script src="js/navegacion.js"></script>
    <script src="js/formularioIntegracion.js"></script>

    <script>
        window.appData = {
            nombrePlan: '<?php echo $planUsuario; ?>',
            estadoSuscripcion: '<?php echo $estadoSuscripcion; ?>',
            sftpActivo: <?php echo $sftpActivo; ?>,
            sftpConfig: <?php
                        echo json_encode([
                            'servidor' => $sftpData['servidor'] ?? '',
                            'puerto' => $sftpData['puerto'] ?? '22',
                            'usuario' => $sftpData['usuario'] ?? '',
                            'contrasena' => '',
                            'rutaDestino' => $sftpData['rutaDestino'] ?? ''
                        ]);
                        ?>
        };
    </script>
    <script src="js/reactivar_plan.js"></script>

</body>

</html>