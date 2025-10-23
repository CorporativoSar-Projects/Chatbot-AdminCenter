<?php
session_start();
require_once 'conexion_bd.php';

$id_adm = $_POST['id_adm'] ?? $_SESSION['id_adm'] ?? null;
if (!$id_adm) {
    echo json_encode(["success" => false, "msg" => "No se recibió id_adm"]);
    exit;
}

// Obtener id_empresa
$stmtEmp = $conexion->prepare("SELECT Empresa_id_emp FROM administrador WHERE id_adm=?");
$stmtEmp->bind_param("i", $id_adm);
$stmtEmp->execute();
$resEmp = $stmtEmp->get_result();
if ($resEmp->num_rows === 0) exit(json_encode(["success"=>false,"msg"=>"Empresa no encontrada"]));
$id_emp = $resEmp->fetch_assoc()['Empresa_id_emp'];

// Obtener configuración del chatbot
$stmtChat = $conexion->prepare("SELECT * FROM chatbot WHERE Administrador_id_adm=? ORDER BY id_chatbot DESC LIMIT 1");
$stmtChat->bind_param("i", $id_adm);
$stmtChat->execute();
$resChat = $stmtChat->get_result();
if ($resChat->num_rows === 0) exit(json_encode(["success"=>false,"msg"=>"No hay configuración de chatbot"]));
$cb = $resChat->fetch_assoc();

// URL empresa y SFTP
$stmtUrl = $conexion->prepare("SELECT url_cs_emp FROM empresa WHERE id_emp=?");
$stmtUrl->bind_param("s", $id_emp);
$stmtUrl->execute();
$resUrl = $stmtUrl->get_result();
$url_cs_emp = $resUrl->num_rows ? $resUrl->fetch_assoc()['url_cs_emp'] : "";

$stmtSftp = $conexion->prepare("SELECT activo FROM integracion_sftp WHERE Empresa_id_emp=?");
$stmtSftp->bind_param("s", $id_emp);
$stmtSftp->execute();
$resSftp = $stmtSftp->get_result();
$activo = $resSftp->num_rows ? (int)$resSftp->fetch_assoc()['activo'] : 0;


$urlInforme1 = $cb['inp_url_informe'] ?? null;
// array de configuración
$config = [
    "id_emp"=>$id_emp,
    "estilos"=>[
        "colorPrimario"=>$cb['colorPrimario'],
        "colorSecundario"=>$cb['colorSecundario'],
        "colorTexto"=>$cb['colorTexto'],
        "colorAcento"=>$cb['colorAcento'],
        "colorRespuestaUsuario"=>$cb['colorRespuestaUsuario'],
        "logo"=>$cb['urlLogotipo'],
        "nombreChatbot"=>$cb['inp_nombre'],
        "burbuja"=>$cb['inp_burbuja'],
        "saludo"=>$cb['inp_saludo']
    ],
    "funcionamiento"=>[
        "urlChatbot"=>$url_cs_emp,
        "integracionActiva"=>(bool)$activo
    ],
   "conversacion" => [
    [
      "tema" => $cb['inp_conversa1'],
      "mensaje" => $cb['inp_mensaje_usuario'],
      "columna" => $cb['inp_columna'],
       "urlInforme" => $urlInforme1 
    ],
    [
        "tema" => $cb['inp_conversa2'],
        "mensaje" => $cb['inp_mensaje_usuario2'],
        "columna" => $cb['inp_columna2'],
       "urlInforme" => $urlInforme1 
    ],
    [
        "tema" => $cb['inp_conversa3'],
        "mensaje" => $cb['inp_mensaje_usuario3'],
        "columna" => $cb['inp_columna3'],
        "urlInforme" => $cb['inp_url_informe3'] ?? null
    ]
],
    "despedida"=>$cb['inp_despedida'],
    "vacantes"=>[]
];

// Llenar vacantes con los links de las conversaciones
foreach ($config['conversacion'] as $conver) {
    if (!empty($conver['urlInforme'])) {
        $config['vacantes'][] = $conver['urlInforme'];
    }
}

// Carpeta de la empresa
$dirEmpresa = __DIR__ . "/../usuarios/$id_emp";
if(!is_dir($dirEmpresa)) mkdir($dirEmpresa, 0777, true);

// Guardar JSON sobrescribiendo
$jsonPath = "$dirEmpresa/config.json";
file_put_contents($jsonPath,json_encode($config, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));


// HTML del chatbot con input hidden para id_emp
$snippet = '<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Chatbot JobHelper</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
</head>
<body>
  <!-- Botón para abrir el chatbot -->
  <div id="chatbot-toggle" class="chat-toggle" >
  <img src="https://chatbot.giintapeinnovahue.com/img/chatbot2.png" alt="Chat" class="toggle-icon">
  <span id="chatText" class="chat-text"></span>
  </div>
 
  <!-- Contenedor del Chatbot -->
  <div id="chatbot-container" class="chatbot-container" style="display: none;">

    <div class="chatbot-header">
      <img src="https://chatbot.giintapeinnovahue.com/img/gi.png" alt="Chatbot" class="chatbot-icon">
      <span class="chatbot-nombre"></span>
      <div class="conteiner">
        <div class="chatbot-min" title="Minimizar" onclick="toggleChatbot()">
          <svg class="icono-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path
              d="M 6 12 C 6 11.449219 6.449219 11 7 11 L 17 11 C 17.550781 11 18 11.449219 18 12 C 18 12.550781 17.550781 13 17 13 L 7 13 C 6.449219 13 6 12.550781 6 12 Z" />
          </svg>
        </div>
        <div class="chatbot-close" title="Cerrar" onclick="cerrar()">
          <svg class="icono-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="miter">
            <path d="M 16 8 L 8 16 M 8 8 L 16 16" />
          </svg>
        </div>
      </div>
    </div>

    <div class="chatbot-body">
      <div id="mensaje-inicial" class="chatbot-message">
        <p></p>
        <div class="chatbot-button-container">
          <button onclick="mostrarPreguntaPerfil()">Buscar vacantes por categoria</button>
          <button onclick="iniciarBusquedaPorUbicacion()">Buscar vacantes por ubicacion</button>
          <button onclick="seguimientoPostulacion()">Seguimiento de mi postulacion</button>
        </div>
      </div>
    </div>

    <!-- Caja de texto y botón para enviar respuestas, inicialmente ocultos -->
    <div id="user-input-container" class="user-input-container" style="display:none;">
      <input type="text" id="user-input" placeholder="Escribe aquí tu respuesta...">
      <button onclick="enviarRespuesta()">Enviar</button>
    </div>
  </div>

   <input type="hidden" id="nomEmp" value="'.$id_emp.'">

  <!-- ubicacion-->
  <!-- Contenedor para seleccionar estado, oculto inicialmente -->
  <div id="seleccion-estado-container" class="chatbot-message" style="display: none;">
    <select id="seleccion-estado">
      
    </select>
    
  </div>
  <!-- Contenedor para seleccionar alcaldía, oculto inicialmente -->
  <div id="seleccion-alcaldia-container" class="chatbot-message" style="display: none;">
    <select id="seleccion-alcaldia">
      <!-- Opciones de alcaldías aquí -->
    </select>
    <!-- El botón para buscar vacantes se muestra después de seleccionar una alcaldía -->
  </div>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://chatbot.giintapeinnovahue.com/script.js"></script>
</body>
</html>';

// Leer contenido para confirmar
echo json_encode([
    "success" => true,
    "jsonPath" => $jsonPath,
    "contenido" => file_get_contents($jsonPath),
    "snippet" => $snippet
]);
