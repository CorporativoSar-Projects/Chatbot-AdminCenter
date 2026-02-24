<?php
// ============================================
// VALIDACIÓN DE SESIÓN Y DATOS EXISTENTES
// ============================================
session_start();

if (!isset($_SESSION['id_adm']) || empty($_SESSION['id_adm'])) {
    header("Location: index.php");
    exit();
}

include 'modelo/conexion_bd.php';

$id_usuario = $_SESSION['id_adm'];
$datos_empresa = [];

// Obtener información existente
try {
    $sql = "SELECT * FROM informacion_empresa WHERE id_usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Si no hay datos, redirigir a la página principal
        header("Location: info_empresa.php");
        exit();
    }
    
    $datos_empresa = $result->fetch_assoc();
} catch (Exception $e) {
    error_log("Error al obtener información: " . $e->getMessage());
    header("Location: info_empresa.php");
    exit();
}

// Procesar actualización
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancelar'])) {
        header("Location: info_empresa.php");
        exit();
    }
    
    if (isset($_POST['actualizar'])) {
        try {
            // Recoger y sanitizar datos
            $nombre_empresa = trim($_POST['nombre_empresa']);
            $resena = trim($_POST['resena']);
            $correo_oficial = trim($_POST['correo_oficial']);
            $direccion = trim($_POST['direccion']);
            $facebook_url = trim($_POST['facebook_url']);
            $instagram_url = trim($_POST['instagram_url']);
            $linkedin_url = trim($_POST['linkedin_url']);
            $mision = trim($_POST['mision']);
            $vision = trim($_POST['vision']);
            $valores = trim($_POST['valores']);
            
            // Validaciones básicas
            if (empty($nombre_empresa) || empty($correo_oficial)) {
                throw new Exception("Nombre de empresa y correo son obligatorios");
            }
            
            if (!filter_var($correo_oficial, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Correo electrónico no válido");
            }
            
            // Actualizar en la base de datos
            $sql_update = "UPDATE informacion_empresa SET 
                          nombre_empresa = ?,
                          resena = ?,
                          correo_oficial = ?,
                          direccion = ?,
                          facebook_url = ?,
                          instagram_url = ?,
                          linkedin_url = ?,
                          mision = ?,
                          vision = ?,
                          valores = ?,
                          fecha_actualizacion = NOW()
                          WHERE id_usuario = ?";
            
            $stmt = $conexion->prepare($sql_update);
            $stmt->bind_param(
                "ssssssssssi",
                $nombre_empresa,
                $resena,
                $correo_oficial,
                $direccion,
                $facebook_url,
                $instagram_url,
                $linkedin_url,
                $mision,
                $vision,
                $valores,
                $id_usuario
            );
            
            if ($stmt->execute()) {
                $mensaje = "Información actualizada correctamente";
                $tipo_mensaje = "success";
                
                // Actualizar datos locales
                $datos_empresa = array_merge($datos_empresa, [
                    'nombre_empresa' => $nombre_empresa,
                    'resena' => $resena,
                    'correo_oficial' => $correo_oficial,
                    'direccion' => $direccion,
                    'facebook_url' => $facebook_url,
                    'instagram_url' => $instagram_url,
                    'linkedin_url' => $linkedin_url,
                    'mision' => $mision,
                    'vision' => $vision,
                    'valores' => $valores
                ]);
            } else {
                throw new Exception("Error al actualizar en la base de datos");
            }
            
        } catch (Exception $e) {
            $mensaje = "Error: " . $e->getMessage();
            $tipo_mensaje = "danger";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/sty.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Editar Información de la Empresa</title>
    <link rel="shortcut icon" href="img/Logo_cabeza.svg">
    <style>
        .paso-container {
            display: none;
            animation: fadeIn 0.5s;
        }
        .paso-container.active {
            display: block;
        }
        .indicador-paso {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .circulo-paso {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 15px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }
        .circulo-paso.active {
            background: #007bff;
            color: white;
            transform: scale(1.1);
        }
        .circulo-paso.completado {
            background: #28a745;
            color: white;
        }
        .linea-pasos {
            flex: 1;
            height: 3px;
            background: #e0e0e0;
            margin: 0 -15px;
            position: relative;
            top: 20px;
        }
        .btn-siguiente, .btn-anterior, .btn-cancelar {
            padding: 10px 30px;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-siguiente {
            background: #007bff;
            color: white;
            border: none;
        }
        .btn-anterior {
            background: #6c757d;
            color: white;
            border: none;
        }
        .btn-cancelar {
            background: #dc3545;
            color: white;
            border: none;
        }
        .btn-actualizar {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 5px;
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border 0.3s;
        }
        .form-control:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
        }
        .contador-caracteres {
            font-size: 12px;
            color: #6c757d;
            text-align: right;
            margin-top: 5px;
        }
        .contador-caracteres.alerta {
            color: #dc3545;
        }
        .social-icon {
            width: 30px;
            height: 30px;
            margin-right: 10px;
            vertical-align: middle;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .botones-accion {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="rectangulo-container">
        <img src="img/LOGOTIPO_IXAH-02.png" width="70px" alt="Logo" class="img-logo-chiq">
    </div>

    <header>
        <nav class="navbar">
            <ul class="filas">
                <li><a href="menu.php" class="txt-home">← Volver al Menú</a></li>
            </ul>
        </nav>

        <div class="user-dropdown">
            <div class="cont-btn-user" id="close-btn-user">
                <button class="btn-user" id="user-btn">
                    <img src="img/user.png" width="30" alt="User Icon" />
                </button>
            </div>
            <!-- Menu lateral (mantener igual que en info_empresa.php) -->
            <div class="dropdown-content" id="dropdown-content">
                <!-- ... mismo contenido que en info_empresa.php ... -->
            </div>
        </div>
    </header>

    <main id="contenidoPrincipal">
        <div class="container" style="max-width: 800px; margin: 40px auto;padding: 120px 25px;">
            <h1 class="text-center mb-4">Editar Información de la Empresa</h1>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-<?php echo $tipo_mensaje; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($mensaje); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="alert alert-info">
                <strong>Está editando la información existente.</strong> Todos los campos marcados con * son obligatorios.
            </div>
            
            <!-- Indicador de Pasos -->
            <div class="indicador-paso">
                <div class="circulo-paso active" data-paso="1">1</div>
                <div class="linea-pasos"></div>
                <div class="circulo-paso" data-paso="2">2</div>
                <div class="linea-pasos"></div>
                <div class="circulo-paso" data-paso="3">3</div>
            </div>

            <!-- Formulario -->
            <form id="formInfoEmpresa" method="POST" action="">
                <!-- Paso 1: Información básica -->
                <div class="paso-container active" id="paso1">
                    <h2 class="mb-4">Paso 1: Información Básica</h2>
                    
                    <div class="form-group">
                        <label class="form-label">Nombre de la empresa *</label>
                        <input type="text" 
                               class="form-control" 
                               name="nombre_empresa" 
                               id="nombre_empresa"
                               value="<?php echo htmlspecialchars($datos_empresa['nombre_empresa'] ?? ''); ?>"
                               required 
                               placeholder="Ej: Tech Solutions SA de CV">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Reseña de la empresa (máx. 800 caracteres)</label>
                        <textarea class="form-control" 
                                  name="resena" 
                                  id="resena" 
                                  rows="6" 
                                  maxlength="800"
                                  placeholder="Describa brevemente su empresa..."><?php echo htmlspecialchars($datos_empresa['resena'] ?? ''); ?></textarea>
                        <div class="contador-caracteres" id="contador-resena">800 caracteres restantes</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correo electrónico oficial *</label>
                        <input type="email" 
                               class="form-control" 
                               name="correo_oficial" 
                               id="correo_oficial"
                               value="<?php echo htmlspecialchars($datos_empresa['correo_oficial'] ?? ''); ?>"
                               required 
                               placeholder="contacto@empresa.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Dirección (ubicación)</label>
                        <input type="text" 
                               class="form-control" 
                               name="direccion" 
                               id="direccion"
                               value="<?php echo htmlspecialchars($datos_empresa['direccion'] ?? ''); ?>"
                               placeholder="Av. Principal #123, Ciudad, País">
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn-siguiente" data-siguiente="2">Siguiente →</button>
                    </div>
                </div>

                <!-- Paso 2: Redes Sociales -->
                <div class="paso-container" id="paso2">
                    <h2 class="mb-4">Paso 2: Redes Sociales</h2>
                    <p class="text-muted mb-4">Ingrese las URL oficiales de sus redes sociales (opcional)</p>

                    <div class="form-group">
                        <label class="form-label">
                            <img src="https://cdn-icons-png.flaticon.com/512/124/124010.png" class="social-icon" alt="Facebook">
                            Facebook
                        </label>
                        <input type="url" 
                               class="form-control" 
                               name="facebook_url" 
                               id="facebook_url"
                               value="<?php echo htmlspecialchars($datos_empresa['facebook_url'] ?? ''); ?>"
                               placeholder="https://facebook.com/tuempresa">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <img src="https://cdn-icons-png.flaticon.com/512/174/174855.png" class="social-icon" alt="Instagram">
                            Instagram
                        </label>
                        <input type="url" 
                               class="form-control" 
                               name="instagram_url" 
                               id="instagram_url"
                               value="<?php echo htmlspecialchars($datos_empresa['instagram_url'] ?? ''); ?>"
                               placeholder="https://instagram.com/tuempresa">
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <img src="https://cdn-icons-png.flaticon.com/512/174/174857.png" class="social-icon" alt="LinkedIn">
                            LinkedIn
                        </label>
                        <input type="url" 
                               class="form-control" 
                               name="linkedin_url" 
                               id="linkedin_url"
                               value="<?php echo htmlspecialchars($datos_empresa['linkedin_url'] ?? ''); ?>"
                               placeholder="https://linkedin.com/company/tuempresa">
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn-anterior" data-anterior="1">← Anterior</button>
                        <button type="button" class="btn-siguiente" data-siguiente="3">Siguiente →</button>
                    </div>
                </div>

                <!-- Paso 3: Filosofía -->
                <div class="paso-container" id="paso3">
                    <h2 class="mb-4">Paso 3: Filosofía Empresarial</h2>
                    <p class="text-muted mb-4">Complete los siguientes campos (máx. 500 caracteres cada uno)</p>

                    <div class="form-group">
                        <label class="form-label">Misión</label>
                        <textarea class="form-control" 
                                  name="mision" 
                                  id="mision" 
                                  rows="4" 
                                  maxlength="500"
                                  placeholder="¿Cuál es el propósito de su empresa?"><?php echo htmlspecialchars($datos_empresa['mision'] ?? ''); ?></textarea>
                        <div class="contador-caracteres" id="contador-mision">500 caracteres restantes</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Visión</label>
                        <textarea class="form-control" 
                                  name="vision" 
                                  id="vision" 
                                  rows="4" 
                                  maxlength="500"
                                  placeholder="¿Hacia dónde quiere llegar su empresa?"><?php echo htmlspecialchars($datos_empresa['vision'] ?? ''); ?></textarea>
                        <div class="contador-caracteres" id="contador-vision">500 caracteres restantes</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Valores</label>
                        <textarea class="form-control" 
                                  name="valores" 
                                  id="valores" 
                                  rows="4" 
                                  maxlength="500"
                                  placeholder="¿Cuáles son los valores fundamentales de su empresa?"><?php echo htmlspecialchars($datos_empresa['valores'] ?? ''); ?></textarea>
                        <div class="contador-caracteres" id="contador-valores">500 caracteres restantes</div>
                    </div>

                    <div class="botones-accion">
                        <button type="submit" name="cancelar" class="btn-cancelar">Cancelar</button>
                        <div>
                            <button type="button" class="btn-anterior me-2" data-anterior="2">← Anterior</button>
                            <button type="submit" name="actualizar" class="btn-actualizar">Actualizar Información</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            let pasoActual = 1;
            const totalPasos = 3;

            // Funciones para contar caracteres
            function actualizarContador(textareaId, contadorId, maxChars) {
                const textarea = document.getElementById(textareaId);
                const contador = document.getElementById(contadorId);
                
                if (textarea && contador) {
                    const caracteresRestantes = maxChars - textarea.value.length;
                    contador.textContent = `${caracteresRestantes} caracteres restantes`;
                    
                    if (caracteresRestantes < 50) {
                        contador.classList.add('alerta');
                    } else {
                        contador.classList.remove('alerta');
                    }
                }
            }

            // Inicializar contadores
            actualizarContador('resena', 'contador-resena', 800);
            actualizarContador('mision', 'contador-mision', 500);
            actualizarContador('vision', 'contador-vision', 500);
            actualizarContador('valores', 'contador-valores', 500);

            // Eventos para contadores en tiempo real
            $('#resena').on('input', function() {
                actualizarContador('resena', 'contador-resena', 800);
            });

            $('#mision').on('input', function() {
                actualizarContador('mision', 'contador-mision', 500);
            });

            $('#vision').on('input', function() {
                actualizarContador('vision', 'contador-vision', 500);
            });

            $('#valores').on('input', function() {
                actualizarContador('valores', 'contador-valores', 500);
            });

            // Función para cambiar de paso
            function cambiarPaso(nuevoPaso) {
                // Validar paso actual antes de avanzar
                if (nuevoPaso > pasoActual && !validarPasoActual()) {
                    return;
                }

                // Ocultar paso actual
                $(`#paso${pasoActual}`).removeClass('active');
                $(`.circulo-paso[data-paso="${pasoActual}"]`).removeClass('active');

                // Mostrar nuevo paso
                $(`#paso${nuevoPaso}`).addClass('active');
                $(`.circulo-paso[data-paso="${nuevoPaso}"]`).addClass('active');

                // Marcar pasos anteriores como completados
                for (let i = 1; i < nuevoPaso; i++) {
                    $(`.circulo-paso[data-paso="${i}"]`).addClass('completado');
                }

                // Actualizar variable
                pasoActual = nuevoPaso;
            }

            // Validar paso actual
            function validarPasoActual() {
                if (pasoActual === 1) {
                    const nombre = $('#nombre_empresa').val().trim();
                    const correo = $('#correo_oficial').val().trim();
                    
                    if (!nombre) {
                        alert('Por favor, ingrese el nombre de la empresa');
                        $('#nombre_empresa').focus();
                        return false;
                    }
                    
                    if (!correo) {
                        alert('Por favor, ingrese el correo electrónico oficial');
                        $('#correo_oficial').focus();
                        return false;
                    }
                    
                    // Validar formato de email
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(correo)) {
                        alert('Por favor, ingrese un correo electrónico válido');
                        $('#correo_oficial').focus();
                        return false;
                    }
                }
                return true;
            }

            // Eventos de botones
            $('.btn-siguiente').click(function() {
                const siguientePaso = $(this).data('siguiente');
                if (siguientePaso) {
                    cambiarPaso(siguientePaso);
                }
            });

            $('.btn-anterior').click(function() {
                const anteriorPaso = $(this).data('anterior');
                if (anteriorPaso) {
                    cambiarPaso(anteriorPaso);
                }
            });

            // Click en indicadores de paso
            $('.circulo-paso').click(function() {
                const pasoClick = $(this).data('paso');
                if (pasoClick <= pasoActual) {
                    cambiarPaso(pasoClick);
                }
            });

            // Validación antes de actualizar
            $('button[name="actualizar"]').click(function(e) {
                if (!validarPasoActual()) {
                    e.preventDefault();
                    cambiarPaso(1);
                    return false;
                }
                
                // Mostrar mensaje de confirmación
                if (!confirm('¿Está seguro de actualizar la información de la empresa?')) {
                    e.preventDefault();
                    return false;
                }
            });

            // Confirmación para cancelar
            $('button[name="cancelar"]').click(function(e) {
                if (!confirm('¿Está seguro de cancelar? Los cambios no guardados se perderán.')) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
</body>
</html>
