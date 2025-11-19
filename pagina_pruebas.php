<?php
session_start();

if (!isset($_SESSION['id_adm'])) {
     header("location: ./index.php?error=2");
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>IXAH Interactivo</title>
  <link rel="stylesheet" href="css/styles_prueba.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
  <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
</head>

<body>
  <!-- Botón para abrir el chatbot -->
  <div id="chatbot-toggle" class="chat-toggle">
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
            <path d="M 6 12 C 6 11.449219 6.449219 11 7 11 L 17 11 C 17.550781 11 18 11.449219 18 12 C 18 12.550781 17.550781 13 17 13 L 7 13 C 6.449219 13 6 12.550781 6 12 Z" />
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

    <div class="chatbot-body" id="chatBox">
         <div id="mensaje-inicial" class="chatbot-message">
    <p></p>
    <div class="chatbot-button-container"></div>
  </div>
      <!-- Aquí se inyectan mensajes dinámicamente -->
    </div>

    <!-- Caja de texto y botón para enviar respuestas -->
    <div id="user-input-container" class="user-input-container" style="display:none;">
      <input type="text" id="user-input" placeholder="Escribe aquí tu respuesta...">
      <button onclick="enviarRespuesta()">Enviar</button>
    </div>
  </div>

  <!-- IDs dinámicos desde la sesión PHP -->
  <input type="hidden" id="idEmp" value="<?php echo $_SESSION['id_emp']; ?>">
  <input type="hidden" id="idChatbot" value="<?php echo $_SESSION['id_chatbot']; ?>">

  <!-- Contenedores auxiliares (ubicación, filtros, etc.) -->
  <div id="seleccion-estado-container" class="chatbot-message" style="display: none;">
    <select id="seleccion-estado"></select>
  </div>

  <div id="seleccion-alcaldia-container" class="chatbot-message" style="display: none;">
    <select id="seleccion-alcaldia"></select>
  </div>

  <!-- Librerías externas -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script src="js/pruebaChatbot.js"></script>
</body>

</html>
