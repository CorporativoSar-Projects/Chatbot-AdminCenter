// js/chat-analisis-unificado.js

const API_BASE = "http://localhost:8000/api";

// Obtener CSRF token
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

// ====== CHAT GENERAL ======

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
        setTimeout(mostrarConfirmacionAyuda, 500);
    } catch (error) {
        console.error("Error final después de reintentos:", error);
        mostrarMensajeBot("⚠️ El servicio de IA no está disponible en este momento. Por favor intenta más tarde.");
    }
}

// ====== FUNCIONES DE MENSAJE ======

function mostrarMensajeBot(msg) {
    addMessage(msg, 'bot-message');
}

function mostrarMensajeUsuario(msg) {
    addMessage(msg, 'user-message');
}

// Unificación: contenedor único
function addMessage(message, type) {
    const chatBox = document.getElementById('chatBox');
    const div = document.createElement('div');
    div.classList.add('chat-message', type);
    div.textContent = message;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}

function addHTMLMessage(html, type) {
    const chatBox = document.getElementById('chatBox');
    const div = document.createElement('div');
    div.classList.add('chat-message', type);
    div.innerHTML = html;
    chatBox.appendChild(div);
    chatBox.scrollTop = chatBox.scrollHeight;
}

// ====== FUNCIONES DE REINTENTOS ======

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

// ====== FUNCIONES DE CONFIRMACIÓN ======

function mostrarConfirmacionAyuda() {
    const confirmacionHTML = `
        <div class="chat-message bot-message">
            <p>¿Puedo ayudarte con algo más?</p>
            <div class="special-buttons" style="margin-top: 10px;">
                <button class="special-btn" onclick="manejarConfirmacion(true)" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">Sí</button>
                <button class="special-btn" onclick="manejarConfirmacion(false)" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">No</button>
            </div>
        </div>
    `;
    addHTMLMessage(confirmacionHTML, 'bot-message');
}

function manejarConfirmacion(respuesta) {
    if (respuesta) {
        addMessage('Sí', 'user-message');
        mostrarOpcionesPrincipales();
    } else {
        addMessage('No', 'user-message');
        mostrarMensajeEspera();
    }
}

function mostrarOpcionesPrincipales() {
    const opcionesHTML = `
        <div class="chat-message bot-message">
            <p>¡Perfecto! ¿En qué más puedo ayudarte?</p>
            <div class="special-buttons">
                <button class="special-btn" onclick="mostrarAnalisisCandidato()">🔍 Análisis de candidatos</button>
                <button class="special-btn" onclick="mejorarDescripcionPuesto()">✏️ Mejorar descripciones de puestos</button>
            </div>
        </div>
    `;
    addHTMLMessage(opcionesHTML, 'bot-message');
}

function mostrarMensajeEspera() {
    const esperaHTML = `
        <div class="chat-message bot-message">
            <p>¡Claro! Estoy aquí pendiente por si necesitas algo.</p>
            <div class="special-buttons" style="margin-top: 10px;">
                <button class="special-btn" onclick="mostrarOpcionesPrincipales()" style="background: linear-gradient(135deg, #002B45 0%, #3ca6e5 100%);">
                    Ahora sí, necesito ayuda
                </button>
            </div>
        </div>
    `;
    addHTMLMessage(esperaHTML, 'bot-message');
}

// ====== ANÁLISIS DE CANDIDATOS ======

let candidatosData = []; // Global, cargar datos reales

function toggleSelectionButtons(show) {
    const buttons = document.querySelectorAll('.btn-select-candidate');
    buttons.forEach(button => button.style.display = show ? 'block' : 'none');
    if (show) toggleImproveButtons(false);
}

function seleccionarParaAnalisis(id) {
    const candidato = candidatosData.find(c => c.id_candidate == id);
    if (candidato) {
        toggleSelectionButtons(false);
        addMessage(`Seleccioné a ${candidato.nombre_candidate} ${candidato.apellidop_candidate} para análisis`, 'user-message');
        iniciarAnalisisCandidato(candidato);
    }
}

function iniciarAnalisisCandidato(candidato) {
    const analysisHTML = `
        <div class="candidate-analysis">
            <h5>🔍 Análisis de ${candidato.nombre_candidate} ${candidato.apellidop_candidate}</h5>
            <div class="analysis-field"><strong>📧 Email:</strong> ${candidato.correo_candidate}</div>
            <div class="analysis-field"><strong>💼 Puesto aplicado:</strong> ${candidato.puesto || 'No especificado'}</div>
            <div class="analysis-field"><strong>📊 Estado del proceso:</strong> 
                <span class="badge badge-${candidato.estado === 'Contratado' ? 'success' : 'warning'}">
                    ${candidato.estado || 'Pendiente'}
                </span>
            </div>
            <div class="analysis-field"><strong>📞 Teléfono:</strong> ${candidato.tel_candidate}</div>
            <div class="analysis-field"><strong>🎯 Análisis IA:</strong> Preparando evaluación detallada...</div>
        </div>
    `;
    addHTMLMessage(analysisHTML, 'bot-message');
    setTimeout(() => mostrarConfirmacionAyuda(), 1000);
}

function mostrarAnalisisCandidato() {
    addMessage('Quiero analizar un candidato', 'user-message');
    document.getElementById('analysis-input-container').style.display = 'none';
    toggleSelectionButtons(true);
    addMessage('Por favor, selecciona un candidato de la tabla haciendo clic en "✅ Seleccionar para Análisis"', 'bot-message');
}

// ====== MEJORA DE DESCRIPCIONES ======

function toggleImproveButtons(show) {
    const buttons = document.querySelectorAll('.btn-improve-job');
    buttons.forEach(button => button.style.display = show ? 'block' : 'none');
    if (show) toggleSelectionButtons(false);
}

function seleccionarParaMejoraPuesto(id) {
    const candidato = candidatosData.find(c => c.id_candidate == id);
    if (candidato) {
        toggleImproveButtons(false);
        addMessage(`Seleccioné a ${candidato.nombre_candidate} ${candidato.apellidop_candidate} para mejorar la descripción del puesto: ${candidato.puesto || 'Sin especificar'}`, 'user-message');
        iniciarMejoraDescripcionPuesto(candidato);
    }
}

async function iniciarMejoraDescripcionPuesto(candidato) {
    const mejoraHTML = `
        <div class="candidate-analysis">
            <h5>✏️ Mejorar Descripción de Puesto</h5>
            <div class="analysis-field"><strong>👤 Candidato:</strong> ${candidato.nombre_candidate} ${candidato.apellidop_candidate}</div>
            <div class="analysis-field"><strong>💼 Puesto actual:</strong> ${candidato.puesto || 'No especificado'}</div>
            <div class="analysis-field"><strong>📧 Email:</strong> ${candidato.correo_candidate}</div>
            <div class="analysis-field"><strong>🔄 Proceso:</strong> Consultando con IA para generar descripción mejorada...</div>
        </div>
    `;
    addHTMLMessage(mejoraHTML, 'bot-message');

    try {
        const response = await fetch(`${API_BASE}/mejorar-descripcion/`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                candidato: {
                    nombre: `${candidato.nombre_candidate} ${candidato.apellidop_candidate}`,
                    email: candidato.correo_candidate,
                    puesto: candidato.puesto
                },
                puesto_actual: candidato.puesto || 'Puesto no especificado'
            })
        });
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
        const data = await response.json();
        if (data.descripcion_mejorada) mostrarDescripcionMejoradaReal(candidato, data.descripcion_mejorada);
        else throw new Error('Sin descripción mejorada');
    } catch (error) {
        console.error('Error al conectar con la API:', error);
        addMessage('⚠️ El servicio de IA no está disponible en este momento. Por favor intenta más tarde.', 'bot-message');
        setTimeout(() => mostrarConfirmacionAyuda(), 500);
    }
}

function mostrarDescripcionMejoradaReal(candidato, descripcionIA) {
    const descripcionMejoradaHTML = `
        <div class="candidate-analysis" style="border-left: 4px solid #ff6b35;">
            <h5>🚀 Descripción Mejorada por IA para: ${candidato.puesto}</h5>
            <div class="analysis-field">
                <strong>🤖 Análisis IA:</strong>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin-top: 10px; white-space: pre-wrap; font-size: 14px; line-height: 1.5;">
                    ${descripcionIA}
                </div>
            </div>
        </div>
        <div class="special-buttons" style="margin-top: 15px;">
            <button class="special-btn" onclick="copiarDescripcionIA('${descripcionIA.replace(/'/g, "\\'").replace(/\n/g, '\\n')}')" style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);">📋 Copiar Descripción</button>
            <button class="special-btn" onclick="personalizarDescripcion(${candidato.id_candidate})" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">🎨 Personalizar Más</button>
        </div>
    `;
    addHTMLMessage(descripcionMejoradaHTML, 'bot-message');
    setTimeout(() => mostrarConfirmacionAyuda(), 1000);
}

function copiarDescripcionIA(descripcion) {
    const descripcionTexto = descripcion.replace(/\\n/g, '\n').replace(/\\'/g, "'");
    navigator.clipboard.writeText(descripcionTexto)
        .then(() => addMessage('✅ Descripción copiada al portapapeles', 'bot-message'))
        .catch(err => addMessage('❌ Error al copiar la descripción', 'bot-message'));
}

function personalizarDescripcion(idCandidato) {
    const candidato = candidatosData.find(c => c.id_candidate == idCandidato);
    addMessage(`Quiero personalizar más la descripción para ${candidato.nombre_candidate}`, 'user-message');
    setTimeout(() => {
        addHTMLMessage(`
            <div class="chat-message bot-message">
                <p>Para personalizar la descripción de "${candidato.puesto}", por favor especifica en el chat:</p>
                <ul>
                    <li>🔹 Nivel de experiencia requerido</li>
                    <li>🔹 Habilidades técnicas específicas</li>
                    <li>🔹 Beneficios adicionales</li>
                    <li>🔹 Modalidad de trabajo (presencial/remoto)</li>
                    <li>🔹 Cualquier otro requerimiento especial</li>
                </ul>
            </div>
        `, 'bot-message');
    }, 1000);
}

function mejorarDescripcionPuesto() {
    addMessage('Quiero mejorar una descripción de puesto', 'user-message');
    document.getElementById('analysis-input-container').style.display = 'none';
    toggleSelectionButtons(false);
    toggleImproveButtons(true);
    addMessage('Por favor, selecciona un candidato de la tabla haciendo clic en "✏️ Mejorar Descripción" para mejorar el puesto al que aplicó', 'bot-message');
}

// ====== EVENTOS DOM ======

document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("chatbot-toggle");
    if (toggle) toggle.addEventListener("click", () => {
        const container = document.getElementById("chatbot-container");
        if (container.style.display === "none" || !container.style.display) crearSesionChat();
    });

    const aiInput = document.getElementById("ai-input");
    const aiSendBtn = document.getElementById("ai-send-btn");

    if (aiSendBtn) aiSendBtn.addEventListener("click", enviarPreguntaIA);
    if (aiInput) aiInput.addEventListener("keypress", (event) => {
        if (event.key === "Enter") {
            event.preventDefault();
            if (aiSendBtn) aiSendBtn.click();
        }
    });
});

