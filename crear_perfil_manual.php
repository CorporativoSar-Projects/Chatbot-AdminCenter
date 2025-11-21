<?php
session_start();
include 'modelo/conexion_bd.php'; // Tu conexión a la base de datos


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $texto = $_POST['descripcion'];
    $id_adm = $_SESSION['id_adm'];

    // 1️⃣ Insertar perfil de puesto manual
    $stmt = $conn->prepare("INSERT INTO job_description (texto_original, fuente, id_adm, creado_en, actualizado_en, publicado) VALUES (?, 'manual', ?, NOW(), NOW(), 0)");
    $stmt->bind_param("si", $texto, $id_adm);
    $stmt->execute();

    // Obtener el ID del nuevo JobDescription
    $job_id = $stmt->insert_id;
    $stmt->close();

    // 2️⃣ Insertar primera revisión
    $stmt2 = $conn->prepare("INSERT INTO job_description_revision (job_description_id, texto_mejorado, creado_por, version, creado_en) VALUES (?, ?, ?, 1, NOW())");
    $stmt2->bind_param("isi", $job_id, $texto, $id_adm);
    $stmt2->execute();
    $stmt2->close();

    // 3️⃣ Redirigir a la lista de perfiles
    header("Location: lista_perfiles.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Perfil Manual - IXAH</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/sty.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
    <style>
        :root {
            --primary-blue: #002B45;
            --secondary-blue: #3ca6e5;
            --accent-yellow: #F9BE21;
            --light-bg: #f3f3f3;
        }

        body {
            background-color: var(--light-bg);
            min-height: 100vh;
            padding-top: 80px;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .form-container {
            background-color: #ffffff;
            border-radius: 15px;
            padding: 30px;
            margin-top: 30px;
            box-shadow: 4px 4px 6px #c5c5c5, -4px -4px 6px #ffffff;
            border: 1px solid #e9ecef;
        }

        .page-title {
            color: var(--primary-blue);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
        }

        .page-subtitle {
            color: #666;
            font-size: 1.1rem;
            text-align: center;
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-blue);
            margin-bottom: 8px;
        }

        .form-control-custom {
            width: 100%;
            padding: 12px 20px;
            margin: 8px 0;
            border: none;
            background-color: #ffffff;
            box-sizing: border-box;
            border-radius: 15px;
            text-decoration: none;
            outline: none;
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
            box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.1);
            font-family: 'Montserrat', sans-serif;
        }

        .form-control-custom:focus {
            border-color: var(--secondary-blue);
            box-shadow: 0 0 0 3px rgba(60, 166, 229, 0.1);
        }

        .btn-primary-custom {
            background-color: var(--accent-yellow);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-primary-custom:hover {
            background-color: #e6ac19;
            transform: translateY(-2px);
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
        }

        .btn-secondary-custom {
            background-color: var(--secondary-blue);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary-custom:hover {
            background-color: #2c96d4;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
        }

        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        .info-box {
            background: linear-gradient(135deg, rgba(0, 43, 69, 0.05), rgba(60, 166, 229, 0.05));
            border-left: 4px solid var(--secondary-blue);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-box p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        .character-count {
            text-align: right;
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 20px;
                margin-top: 20px;
            }

            .page-title {
                font-size: 2rem;
            }

            .button-group {
                flex-direction: column;
                align-items: center;
            }

            .btn-primary-custom,
            .btn-secondary-custom {
                width: 100%;
                max-width: 300px;
            }
        }
    </style>
</head>

<body>
    <!-- Header igual al de tu sty.css -->
    <div class="rectangulo-container">
        <img src="img/Logo_cabeza.svg" width="70px" alt="Logo" class="img-logo-chiq" />
    </div>

    <header>
        <nav class="navbar">
            <ul class="filas">
                <!-- Menú según tu estructura actual -->
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

    <main class="main-container">
        <div class="form-container">
            <h1 class="page-title">📝 Crear Perfil de Puesto</h1>
            <p class="page-subtitle">Ingresa la descripción completa del puesto de trabajo</p>

            <div class="info-box">
                <p><strong>💡 Información:</strong> Este perfil se guardará como borrador y podrás mejorarlo posteriormente con IA.</p>
            </div>

            <form method="POST" action="" id="perfilForm">
                <div class="form-group">
                    <label for="descripcion" class="form-label">Descripción del Puesto:</label>
                    <textarea
                        class="form-control-custom"
                        name="descripcion"
                        id="descripcion"
                        rows="12"
                        required
                        placeholder="Ingresa la descripción completa del puesto. Incluye responsabilidades, requisitos, habilidades deseadas, y cualquier información relevante..."
                        oninput="updateCharacterCount(this)"></textarea>
                    <div class="character-count" id="charCount">0 caracteres</div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-primary-custom">
                        💾 Guardar Perfil
                    </button>
                    <a href="lista_perfiles.php" class="btn-secondary-custom">
                        ← Volver a Lista
                    </a>
                </div>
            </form>
        </div>

        <!-- lista_perfiles.php -->
        <div class="main-container">
            <div class="form-container">
                <h1 class="page-title">📋 Lista de Perfiles de Puesto</h1>
                <p class="page-subtitle">Gestiona todos los perfiles de puesto creados en el sistema</p>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="crear_perfil_manual.php" class="btn btn-primary-custom">
                        ➕ Crear Nuevo Perfil
                    </a>
                    <button id="btnRecargarPerfiles" class="btn btn-secondary-custom">
                        🔄 Actualizar Lista
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Descripción</th>
                                <th>Fuente</th>
                                <th>Fecha Creación</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaPerfilesBody">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">Cargando...</span>
                                    </div>
                                    <p class="mt-2">Cargando perfiles...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>

    <!-- jQuery y Bootstrap JavaScript -->
    <script src="js/crearperfilmanual.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function updateCharacterCount(textarea) {
            const charCount = textarea.value.length;
            document.getElementById('charCount').textContent = charCount + ' caracteres';
        }

        // Inicializar el contador de caracteres
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('descripcion');
            updateCharacterCount(textarea);
        });

        // Validación del formulario
        document.getElementById('perfilForm').addEventListener('submit', function(e) {
            const descripcion = document.getElementById('descripcion').value.trim();
            if (descripcion.length < 50) {
                e.preventDefault();
                alert('Por favor, ingresa una descripción más detallada (mínimo 50 caracteres).');
                return false;
            }

            if (descripcion.length > 10000) {
                e.preventDefault();
                alert('La descripción es demasiado larga. Por favor, reduce el texto a menos de 10,000 caracteres.');
                return false;
            }

            // Mostrar mensaje de carga
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '⏳ Guardando...';
            submitBtn.disabled = true;
        });
    </script>
</body>

</html>