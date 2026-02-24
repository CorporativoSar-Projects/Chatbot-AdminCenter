// Chatbot IA dinámico - se inserta directamente en la página
document.addEventListener("DOMContentLoaded", function () {

    // --- Crear el botón flotante ---
    const chatbotToggle = document.createElement("div");
    chatbotToggle.id = "chatbot-toggle";
    chatbotToggle.className = "chat-toggle";
    chatbotToggle.innerHTML = `
        <img src="https://chatbot.giintapeinnovahue.com/img/chatbot2.png" alt="Chat" class="toggle-icon">
        <span id="chatText" class="chat-text">Chat</span>
    `;
    document.body.appendChild(chatbotToggle);

    // --- Crear contenedor principal del chatbot ---
    const chatbotContainer = document.createElement("div");
    chatbotContainer.id = "chatbot-container";
    chatbotContainer.className = "chatbot-container";
    document.body.appendChild(chatbotContainer);

    chatbotContainer.innerHTML = `
    <div class="chatbot-header">
        <img src="https://chatbot.giintapeinnovahue.com/img/gi.png" alt="Chatbot" class="chatbot-icon">
        <span class="chatbot-nombre">IXAH - Asistente IA</span>
        <div class="conteiner">
            <div class="chatbot-min" title="Minimizar">-</div>
            <div class="chatbot-close" title="Cerrar">×</div>
        </div>
    </div>
    <div class="chatbot-body" id="chatBox" style="
        flex: 1; 
        min-height: 400px; 
        max-height: 500px; 
        overflow-y: auto; 
        padding: 15px; 
        display: flex; 
        flex-direction: column;
        gap: 10px;
        background-color: #f7eeee;
    ">
        <div class="chatbot-message bot">
            <p>Hola, soy IXAH. Puedo proporcionarte información sobre Candidatos, Vacantes y Procesos actuales.</p>
            <div class="chatbot-button-container">
                <button class="chatbot-btn" data-funcion="candidatos">Candidatos</button>
                <button class="chatbot-btn" data-funcion="vacantes">Vacantes</button>
                <button class="chatbot-btn" data-funcion="procesos">Procesos actuales</button>
            </div>
        </div>
    </div>
    <div class="user-input-container" style="
        display: flex; 
        gap: 10px; 
        padding: 10px; 
        border-top: 1px solid #ccc;
        background-color: #fff;
    ">
        <input type="text" id="ai-input" placeholder="Escribe tu pregunta..." style="
            flex: 1; 
            padding: 10px; 
            border-radius: 20px; 
            border: 1px solid #ccc;
        " />
        <button id="ai-send-btn" style="
            padding: 10px 15px; 
            border-radius: 20px; 
            background-color: #FB8500; 
            color: black; 
            border: none; 
            cursor: pointer;
        ">Enviar</button>
    </div>
    `;


    // --- Variables de control ---
    const toggleBtn = document.getElementById("chatbot-toggle");
    const closeBtn = chatbotContainer.querySelector(".chatbot-close");
    const minBtn = chatbotContainer.querySelector(".chatbot-min");
    const chatBody = chatbotContainer.querySelector(".chatbot-body");

    // --- Abrir / cerrar chatbot con animación ---
    toggleBtn.addEventListener("click", () => {
        chatbotContainer.classList.toggle("open");
        // Si no tiene display block, se lo ponemos
        chatbotContainer.style.display = "block";
    });

    // --- Cerrar chatbot ---
    closeBtn.addEventListener("click", () => {
        chatbotContainer.classList.remove("open");
        setTimeout(() => {
            chatbotContainer.style.display = "none";
        }, 300); // espera la animación
    });

    // --- Minimizar cuerpo ---
    minBtn.addEventListener("click", () => {
        if(chatBody.style.display === "none" || chatBody.style.display === "") {
            chatBody.style.display = "block";
        } else {
            chatBody.style.display = "none";
        }
    });

    // --- Función para agregar mensajes ---
    function agregarMensaje(texto, tipo = "bot") {
        const msg = document.createElement("div");
        msg.className = `chatbot-message ${tipo}`;
        msg.innerHTML = `<p>${texto}</p>`;
        chatBody.appendChild(msg);
        chatBody.scrollTop = chatBody.scrollHeight;
    }

    // --- Manejo de botones internos ---
    const botones = chatbotContainer.querySelectorAll(".chatbot-btn");
    botones.forEach(btn => {
        btn.addEventListener("click", () => {
            const func = btn.dataset.funcion;
            agregarMensaje(btn.textContent, "user");

            if(func === "candidatos") {
                agregarMensaje("Mostrando información de candidatos activos en tiempo real...");
            } else if(func === "vacantes") {
                agregarMensaje("Mostrando vacantes disponibles actualmente...");
            } else if(func === "procesos") {
                agregarMensaje("Mostrando procesos de reclutamiento en curso...");
            }
        });
    });

    // --- Manejo del input de texto ---
    const input = document.getElementById("ai-input");
    const sendBtn = document.getElementById("ai-send-btn");

    sendBtn.addEventListener("click", () => {
        const pregunta = input.value.trim();
        if(!pregunta) return;
        agregarMensaje(pregunta, "user");
        input.value = "";
        agregarMensaje("Procesando tu pregunta sobre candidatos, vacantes o procesos...");
    });

    // Permitir enviar con Enter
    input.addEventListener("keydown", (e) => {
        if(e.key === "Enter") sendBtn.click();
    });
});
