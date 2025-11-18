const API_BASE = "http://localhost:8000/api"; 

function getCookie(name) {
  let cookieValue = null;
  if (document.cookie && document.cookie !== "") {
    const cookies = document.cookie.split(";");
    for (let i = 0; i < cookies.length; i++) {
      const cookie = cookies[i].trim();
      if (cookie.substring(0, name.length + 1) === name + "=") {
        cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
        break;
      }
    }
  }
  return cookieValue;
}
const csrftoken = getCookie("csrftoken");

async function crearSesionChat(userId = null) {
  try {
    const res = await fetch(`${API_BASE}/start/`, {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRFToken": csrftoken },
      body: JSON.stringify({ user_id: userId }),
    });

    if (!res.ok) throw new Error(await res.text());
    const data = await res.json();

    const container = document.getElementById("chatbot-container");
    container.dataset.sessionId = data.session_id;

    iniciarChatIA();
  } catch (error) {
    console.error("Error iniciando sesión de chat:", error);
  }
}

function iniciarChatIA() {
  mostrarMensajeBot("Este chat usa IA para ayudarte. Las respuestas son automáticas.");
  document.getElementById("ai-chat-container").style.display = "flex";
}

async function enviarPreguntaIA() {
  const input = document.getElementById("ai-input");
  const texto = input.value.trim();
  if (!texto) return;

  mostrarMensajeUsuario(texto);
  input.value = "";

  const sessionContainer = document.getElementById("chatbot-container");
  const sessionId = sessionContainer?.dataset?.sessionId;

  if (!sessionId) {
    console.error("No se encontró sessionId");
    mostrarMensajeBot("⚠️ No hay sesión de chat activa.");
    return;
  }

  try {
    const data = await fetchConReintentos(`${API_BASE}/chat/${sessionId}/send/`, {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-CSRFToken": csrftoken },
      body: JSON.stringify({ message: texto }),
    }, 2); // 2 reintentos

    mostrarMensajeBot(data.reply);
    
    // MOSTRAR CONFIRMACIÓN DESPUÉS DE LA RESPUESTA DE IA
    setTimeout(mostrarConfirmacionAyudaIA, 500);

  } catch (error) {
    console.error("Error final después de reintentos:", error);
    mostrarMensajeBot("⚠️ El servicio de IA no está disponible en este momento. Por favor intenta más tarde.");
  }
}

// MODIFICACIÓN: Usar la misma función de confirmación que pruebaChatbot.js
function mostrarConfirmacionAyudaIA() {
  // Verificar si las funciones globales existen (definidas en pruebaChatbot.js)
  if (typeof confirmacionAyuda === 'function') {
    // Si existe la función confirmacionAyuda, usarla directamente
    confirmacionAyuda();
  } else {
    // Fallback: crear una versión básica si no existen las funciones
    mostrarConfirmacionBasica();
  }
}

// Fallback en caso de que no existan las funciones de pruebaChatbot.js
function mostrarConfirmacionBasica() {
  const chatBox = document.getElementById("chatBox");
  
  const divMensaje = document.createElement("div");
  divMensaje.className = "chatbot-message";
  divMensaje.innerHTML = "<p>¿Puedo ayudarte con algo más?</p>";
  
  const botonesDiv = document.createElement("div");
  botonesDiv.className = "chatbot-message-buttons";
  botonesDiv.style.marginTop = "20px";
  botonesDiv.style.display = "flex";
  botonesDiv.style.gap = "10px";
  
  const btnSi = document.createElement("button");
  btnSi.textContent = "Sí";
  btnSi.style.padding = "8px 16px";
  btnSi.style.borderRadius = "15px";
  btnSi.style.border = "none";
  btnSi.style.cursor = "pointer";
  btnSi.style.backgroundColor = "#007bff";
  btnSi.style.color = "white";
  
  const btnNo = document.createElement("button");
  btnNo.textContent = "No";
  btnNo.style.padding = "8px 16px";
  btnNo.style.borderRadius = "15px";
  btnNo.style.border = "none";
  btnNo.style.cursor = "pointer";
  btnNo.style.backgroundColor = "#6c757d";
  btnNo.style.color = "white";
  
  botonesDiv.appendChild(btnSi);
  botonesDiv.appendChild(btnNo);
  divMensaje.appendChild(botonesDiv);
  chatBox.appendChild(divMensaje);
  chatBox.scrollTop = chatBox.scrollHeight;
  
  btnSi.addEventListener("click", () => {
    if (typeof funcionSi === 'function') {
      funcionSi();
    } else {
      mostrarMensajeBot("¡Perfecto! ¿En qué más puedo ayudarte?");
    }
    btnSi.disabled = true;
    btnNo.disabled = true;
  });
  
  btnNo.addEventListener("click", () => {
    if (typeof funcionNo === 'function') {
      funcionNo();
    } else {
      mostrarMensajeBot("¡Gracias por usar nuestro asistente virtual!");
    }
    btnSi.disabled = true;
    btnNo.disabled = true;
  });
}

async function fetchConReintentos(url, options, intentos = 2) {
  for (let i = 0; i <= intentos; i++) {
    try {
      const res = await fetch(url, options);
      if (!res.ok) throw new Error(await res.text());
      return await res.json();
    } catch (error) {
      console.warn(`Intento ${i + 1} fallido:`, error);
      if (i === intentos) throw error;
      await new Promise(r => setTimeout(r, 500));
    }
  }
}

function mostrarMensajeBot(msg) {
  const chatBox = document.getElementById("chatBox");
  const div = document.createElement("div");
  div.className = "chatbot-message";
  div.innerHTML = `<p>${msg}</p>`;
  chatBox.appendChild(div);
  chatBox.scrollTop = chatBox.scrollHeight;
}

function mostrarMensajeUsuario(msg) {
  const chatBox = document.getElementById("chatBox");
  const div = document.createElement("div");
  div.className = "user-message";
  div.innerHTML = `<p>${msg}</p>`;
  chatBox.appendChild(div);
  chatBox.scrollTop = chatBox.scrollHeight;
}

document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.getElementById("chatbot-toggle");
  if (toggle) {
    toggle.addEventListener("click", () => {
      const container = document.getElementById("chatbot-container");
      const estabaCerrado = container.style.display === "none" || !container.style.display;
      if (estabaCerrado) {
        crearSesionChat();
      }
    });
  }

  const aiInput = document.getElementById("ai-input");
  const aiSendBtn = document.getElementById("ai-send-btn");
  const chatBox = document.getElementById("chatBox");

  if (aiSendBtn) {
    aiSendBtn.addEventListener("click", async () => {
      const sessionContainer = document.getElementById("chatbot-container");
      const sessionId = sessionContainer?.dataset?.sessionId;

      if (!sessionId) {
        console.error("No se encontró sessionId");
        mostrarMensajeBot("⚠️ No hay sesión de chat activa.");
        return;
      }

      const userMessage = aiInput.value.trim();
      if (!userMessage) return;

      const userBubble = document.createElement("div");
      userBubble.classList.add("chatbot-message", "user-message");
      userBubble.innerHTML = `<p>${userMessage}</p>`;
      chatBox.appendChild(userBubble);
      chatBox.scrollTop = chatBox.scrollHeight;

      aiInput.value = "";

      try {
        const response = await fetch(`${API_BASE}/chat/${sessionId}/send/`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ message: userMessage }),
        });

        if (!response.ok) {
          console.error("Error en respuesta:", await response.text());
          mostrarMensajeBot("⚠️ El servicio de IA no está disponible en este momento. Por favor intenta más tarde.");
          return;
        }

        const data = await response.json();
        mostrarMensajeBot(data.reply);
        
        // MOSTRAR CONFIRMACIÓN DESPUÉS DE LA RESPUESTA DE IA
        setTimeout(mostrarConfirmacionAyudaIA, 500);

      } catch (error) {
        console.error("Error al consultar la IA:", error);
        mostrarMensajeBot("⚠️ El servicio de IA no está disponible en este momento. Por favor intenta más tarde.");
      }
    });
  }

  // También agregar evento para Enter key en el input de IA
  if (aiInput) {
    aiInput.addEventListener("keypress", function(event) {
      if (event.key === "Enter") {
        event.preventDefault();
        const aiSendBtn = document.getElementById("ai-send-btn");
        if (aiSendBtn) aiSendBtn.click();
      }
    });
  }
});