// js/IA/analisisIA.js

const API_BASE = "http://localhost:8000/api";

// Chat persistente

// js/IA/analisisIA.js - VERSIÓN SIMPLE Y FUNCIONAL

// ============================================
// SISTEMA DE PERSISTENCIA LOCAL PARA EL CHAT
// ============================================

// ID único para el usuario (se pasa desde PHP)
const getUserId = () => {
  try {
    if (typeof window.userId !== "undefined" && window.userId) {
      return window.userId;
    }

    const userElement = document.querySelector("[data-user-id]");
    if (userElement) {
      return userElement.getAttribute("data-user-id");
    }

    return "user_" + btoa(window.location.pathname).substring(0, 10);
  } catch (e) {
    return "default_user";
  }
};

const CHAT_STORAGE_KEY = `ixah_chat_v3_${getUserId()}`;

// Sistema principal de chat persistente
// Sistema principal de chat
const ChatSystem = {
  messages: [],
  isInitialized: false,

  // Inicializar el sistema
  init() {
    if (this.isInitialized) return;

    console.log("🚀 Inicializando ChatSystem (sessionStorage)...");

    // Cargar mensajes guardados
    this.load();

    // Configurar eventos
    this.setupEvents();

    // Renderizar mensajes
    this.render();

    this.isInitialized = true;
    console.log(
      "✅ ChatSystem inicializado con",
      this.messages.length,
      "mensajes"
    );
  },

  // Cargar desde sessionStorage
  load() {
    try {
      // ¡sessionStorage se limpia al cerrar pestaña!
      const saved = sessionStorage.getItem(CHAT_STORAGE_KEY);
      if (saved) {
        this.messages = JSON.parse(saved);
        console.log(
          "📥 Chat cargado desde sesión:",
          this.messages.length,
          "mensajes"
        );
      } else {
        this.createInitialChat();
      }
    } catch (error) {
      console.error("❌ Error cargando chat:", error);
      this.messages = [];
      this.createInitialChat();
    }
  },

  // Crear chat inicial
  createInitialChat() {
    this.messages = [
      {
        tipo: "bot",
        texto: "¡Hola! Soy tu asistente de IA. ¿En qué puedo ayudarte hoy?",
        html: `¡Hola! Soy tu asistente de IA. ¿En qué puedo ayudarte hoy?
            <div class="special-buttons">
                <button class="special-btn" onclick="activarComparacionCV()">🔍 Análisis de candidatos</button>
                <button class="special-btn" onclick="mostrarOpcionesMejoraDescripcion()">✏️ Mejorar descripción de puesto</button>
                <!--<button class="special-btn" onclick="procesarSAPSSFF()">📊 Descripción ATS</button>-->
            </div>`,
        timestamp: new Date().toISOString(),
      },
    ];
    this.save();
  },

  // Guardar en sessionStorage
  save() {
    try {
      if (this.messages.length > 100) {
        this.messages = this.messages.slice(-100);
      }
      // ¡sessionStorage se limpia al cerrar pestaña!
      sessionStorage.setItem(CHAT_STORAGE_KEY, JSON.stringify(this.messages));
    } catch (error) {
      console.error("❌ Error guardando chat:", error);
    }
  },

  // Agregar mensaje
  add(tipo, texto, html = null) {
    const newMessage = {
      tipo: tipo,
      texto: texto,
      html: html || texto,
      timestamp: new Date().toISOString(),
    };

    this.messages.push(newMessage);
    this.save();
    this.render();

    console.log("💬 Mensaje agregado:", {
      tipo,
      texto: texto?.substring(0, 50),
    });

    return newMessage;
  },

  // Renderizar en pantalla
  render() {
    const chatBody = document.getElementById("chat-body");
    if (!chatBody) {
      setTimeout(() => this.render(), 200);
      return;
    }

    const specialContainers = [
      "opciones-mejora-container",
      "manual-description-container",
      "analysis-input-container",
    ];

    const savedContainers = [];
    specialContainers.forEach((id) => {
      const container = document.getElementById(id);
      if (container && chatBody.contains(container)) {
        savedContainers.push({
          element: container,
          display: container.style.display,
        });
        chatBody.removeChild(container);
      }
    });

    const children = Array.from(chatBody.children);
    children.forEach((child) => {
      if (child.classList.contains("chat-message")) {
        chatBody.removeChild(child);
      }
    });

    this.messages.forEach((msg) => {
      const messageDiv = document.createElement("div");
      messageDiv.className = `chat-message ${msg.tipo}-message`;

      if (msg.html && msg.html.includes("<")) {
        messageDiv.innerHTML = msg.html;
      } else {
        messageDiv.textContent = msg.texto || "";
      }

      chatBody.appendChild(messageDiv);
    });

    savedContainers.forEach((item) => {
      chatBody.appendChild(item.element);
      if (item.display) {
        item.element.style.display = item.display;
      }
    });

    setTimeout(() => {
      chatBody.scrollTop = chatBody.scrollHeight;
    }, 50);
  },

  // Configurar eventos del input del chat
  setupEvents() {
    const sendBtn = document.getElementById("send-btn");
    const chatInput = document.getElementById("chat-input");

    if (sendBtn && chatInput) {
      const sendMessage = () => {
        const text = chatInput.value.trim();
        if (text) {
          chatInput.value = "";
          this.add("user", text);

          setTimeout(() => {
            const responses = [
              `He procesado: "${text}". ¿En qué más puedo ayudarte?`,
              `Entendido. ¿Quieres analizar algún candidato en particular?`,
              `Perfecto. Puedo ayudarte con análisis de CVs, mejora de descripciones de puestos, o comparaciones con SAP.`,
            ];
            const response =
              responses[Math.floor(Math.random() * responses.length)];
            this.add("bot", response);
          }, 800);
        }
      };

      sendBtn.addEventListener("click", sendMessage);
      chatInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") sendMessage();
      });
    }
  },

  // Limpiar chat manualmente
  clear() {
    // Limpiar sessionStorage
    sessionStorage.removeItem(CHAT_STORAGE_KEY);
    this.messages = [this.messages[0]];
    this.save();
    this.render();
  },

  // Debug
  debug() {
    console.log("🔍 ChatSystem Debug:");
    console.log("- Clave:", CHAT_STORAGE_KEY);
    console.log("- Mensajes:", this.messages.length);
    console.log(
      "- sessionStorage:",
      sessionStorage.getItem(CHAT_STORAGE_KEY) ? "SÍ" : "NO"
    );
    return this.messages;
  },
};

// ============================================
// SOBREESCRIBIR FUNCIONES EXISTENTES
// ============================================

// Guardar funciones originales (por si acaso)
window._originalAddMessage = window.addMessage;
window._originalAddHTMLMessage = window.addHTMLMessage;

// Sobrescribir addMessage
window.addMessage = function (text, type = "bot-message") {
  const tipo = type === "user-message" ? "user" : "bot";
  return ChatSystem.add(tipo, text);
};

// Sobrescribir addHTMLMessage
window.addHTMLMessage = function (html, type = "bot-message") {
  const tipo = type === "user-message" ? "user" : "bot";
  return ChatSystem.add(tipo, "", html);
};

// ============================================
// INICIALIZACIÓN AUTOMÁTICA
// ============================================

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", function () {
  console.log("📄 DOM cargado, inicializando ChatSystem...");
  ChatSystem.init();

  // Depuración: ver estado cada 30 segundos
  setInterval(() => {
    console.log("⏱️ Chat status:", ChatSystem.messages.length, "mensajes");
  }, 30000);
});

// Función global para debug (opcional)
window.debugChat = () => ChatSystem.debug();

// Función para limpiar manualmente (para usar en los enlaces)
window.limpiarChatYRedirigir = function (url) {
  console.log("🧹 Limpiando chat y redirigiendo a:", url);

  // 1. Limpiar sessionStorage
  sessionStorage.removeItem(CHAT_STORAGE_KEY);

  // 2. También limpiar localStorage por si acaso
  localStorage.removeItem(`ixah_chat_v3_${getUserId()}`);

  // 3. Redirigir
  setTimeout(() => {
    window.location.href = url;
  }, 100);
};

// ============================================
// SOLUCIÓN PARA LIMPIAR AL SALIR (AGREGAR EN HTML)
// ============================================

// PRIMERO definir toggleComparisonButtons
function toggleComparisonButtons(show) {
  const buttons = document.querySelectorAll(".btn-compare-candidate");
  buttons.forEach((button) => {
    button.style.display = show ? "block" : "none";
  });

  // Ocultar otros botones cuando se muestran los de comparación
  if (show) {
    toggleSelectionButtons(false);
    toggleImproveButtons(false);
  }
}

// LUEGO las otras funciones
function toggleSelectionButtons(show) {
  const buttons = document.querySelectorAll(".btn-select-candidate");
  buttons.forEach((button) => {
    button.style.display = show ? "block" : "none";
  });
  // Ocultar botones de mejora cuando se muestran los de análisis
  if (show) {
    toggleImproveButtons(false);
  }
}

function toggleImproveButtons(show) {
  const buttons = document.querySelectorAll(".btn-improve-job");
  buttons.forEach((button) => {
    button.style.display = show ? "block" : "none";
  });

  // Ocultar botones de análisis cuando se muestran los de mejora
  if (show) {
    toggleSelectionButtons(false);
  }
}

// Modifica la función seleccionarParaAnalisis
function seleccionarParaAnalisis(id) {
  const candidato = candidatosData.find((c) => c.id_candidate == id);
  if (candidato) {
    // Ocultar todos los botones de selección
    toggleSelectionButtons(false);

    // Mostrar mensaje en el chat
    /*
    addMessage(
      `Seleccioné a ${candidato.nombre_candidate} ${candidato.apellidop_candidate} para análisis y recomendación de vacantes`,
      "user-message"
    );*/

    // Usar el sistema persistente
    chatPersistente.agregar(
      "user",
      `Seleccioné a ${candidato.nombre_candidate} ${candidato.apellidop_candidate} para análisis y recomendación de vacantes`
    );

    // Iniciar función de análisis CON RECOMENDACIÓN DE VACANTES
    iniciarAnalisisYRecomendacion(candidato);
  }
}

// Nueva función para análisis + recomendación
async function iniciarAnalisisYRecomendacion(candidato) {
  /*
  console.log(
    "Candidato seleccionado para análisis y recomendación:",
    candidato
  );*/

  // Mostrar análisis básico
  const analysisHTML = `
        <div class="candidate-analysis">
            <h5>Compatibiilidad de ${candidato.nombre_candidate} ${candidato.apellidop_candidate
    }</h5>
            <div class="analysis-field">
                <strong>📧 Email:</strong> ${candidato.correo_candidate}
            </div>
            <div class="analysis-field">
                <strong>💼 Puesto aplicado:</strong> ${candidato.puesto || "No especificado"
    }
            </div>
            <div class="analysis-field">
                <strong>🔄 Proceso:</strong> Analizando CV y buscando vacantes recomendadas...
            </div>
        </div>
    `;

  addHTMLMessage(analysisHTML, "bot-message");

  // Mostrar indicador de carga
  const loadingMessage = mostrarMensajeCarga(
    "Analizando CV y buscando vacantes recomendadas..."
  );

  try {
    // 1. Primero obtener el texto del CV
    const cvResponse = await fetch(
      `${API_BASE}/obtener_texto_cv/${candidato.id_candidate}/`
    );
    const cvData = await cvResponse.json();

    if (!cvData.cv_texto || cvData.cv_texto.startsWith("ERROR:")) {
      throw new Error("No se pudo obtener un texto válido del CV");
    }

    // Llamar a la función de recomendación de vacantes
    if (loadingMessage) {
      loadingMessage.querySelector(".loading-text").textContent =
        "Analizando habilidades y buscando vacantes compatibles...";
    }

    // 2. Llamar a la función de recomendación de vacantes
    const recomendacionResponse = await fetch(
      `${API_BASE}/recomendar-vacantes/`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          cv_texto: cvData.cv_texto,
        }),
      }
    );

    if (!recomendacionResponse.ok) {
      const errorData = await recomendacionResponse.json().catch(() => ({}));
      const msg = errorData.error || `HTTP ${recomendacionResponse.status}`;
      throw new Error(msg);
    }

    // 3. Procesando respuesta
    if (loadingMessage) {
      loadingMessage.querySelector(".loading-text").textContent =
        "Generando recomendaciones personalizadas...";
    }

    const recomendacionData = await recomendacionResponse.json();

    if (recomendacionData.error) {
      throw new Error(recomendacionData.error);
    }

    // 4. Ocultar mensaje de carga
    ocultarMensajeCarga();

    // 3. Mostrar resultados de recomendación
    mostrarRecomendacionesVacantes(
      candidato,
      recomendacionData.recomendaciones
    );
  } catch (error) {
    console.error("Error en análisis y recomendación:", error);

    // Ocultar mensaje de carga
    ocultarMensajeCarga();

    const errorHTML = `
      <div class="chat-message bot-message">
        <div style="color: #dc3545; font-weight: bold;">❌ Error en el análisis</div>
        <p>No se pudo completar la recomendación de vacantes: ${error.message}</p>
      </div>
    `;
    addHTMLMessage(errorHTML, "bot-message");

    setTimeout(() => {
      mostrarConfirmacionAyuda();
    }, 1000);
  }
}

// Función para mostrar recomendaciones
function mostrarRecomendacionesVacantes(candidato, recomendaciones) {
  const resultadoHTML = `
        <div class="candidate-analysis" style="border-left: 4px solid #17a2b8;">
            <h5>🎯 Vacantes Recomendadas para ${candidato.nombre_candidate}</h5>
            <div class="analysis-field">
                <strong>🤖 Análisis IA:</strong>
                <div style="background: #e8f4fd; padding: 15px; border-radius: 10px; margin-top: 10px; white-space: pre-wrap; font-size: 14px; line-height: 1.5;">
                    ${recomendaciones}
                </div>
            </div>
        </div>
        
        <div class="special-buttons" style="margin-top: 15px;">
            <button class="special-btn" onclick="procesarSAPSSFF()" 
                    style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);">
                📊 Ver Todas las Vacantes SAP
            </button>
            <button class="special-btn" onclick="activarComparacionCV()" 
                    style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                🔍 Comparar con Vacante Específica
            </button>
        </div>
    `;

  addHTMLMessage(resultadoHTML, "bot-message");

  setTimeout(() => {
    mostrarConfirmacionAyuda();
  }, 1000);
}

// Función que se ejecuta cuando se selecciona un candidato
function iniciarAnalisisCandidato(candidato) {
  console.log("Candidato seleccionado para compatibilidad:", candidato);

  // Mostrar análisis básico en el chat
  const analysisHTML = `
        <div class="candidate-analysis">
            <h5>Compatibilidad de ${candidato.nombre_candidate} ${candidato.apellidop_candidate
    }</h5>
            <div class="analysis-field">
                <strong>📧 Email:</strong> ${candidato.correo_candidate}
            </div>
            <div class="analysis-field">
                <strong>💼 Puesto aplicado:</strong> ${candidato.puesto || "No especificado"
    }
            </div>
            <div class="analysis-field">
                <strong>📊 Estado del proceso:</strong> 
                <span class="badge badge-${candidato.estado === "Contratado" ? "success" : "warning"
    }">
                    ${candidato.estado || "Pendiente"}
                </span>
            </div>
            <div class="analysis-field">
                <strong>📞 Teléfono:</strong> ${candidato.tel_candidate}
            </div>
            <div class="analysis-field">
                <strong>🎯 Análisis IA:</strong> Preparando evaluación detallada...
            </div>
        </div>
    `;

  addHTMLMessage(analysisHTML, "bot-message");

  // Mostrar confirmación después del análisis
  setTimeout(() => {
    mostrarConfirmacionAyuda();
  }, 1000);
}

// Función modificada para mostrar análisis de candidatos
function mostrarAnalisisCandidato() {
  /*
  addMessage("Quiero analizar un candidato", "user-message");
  */
  chatPersistente.agregar("user", "Quiero analizar un candidato");

  // Ocultar el input de búsqueda por nombre
  document.getElementById("analysis-input-container").style.display = "none";

  // Mostrar botones de selección en la tabla
  toggleSelectionButtons(true);

  // Mensaje instructivo
  /*
  addMessage(
    'Por favor, selecciona un candidato de la tabla haciendo clic en "✅ Seleccionar para Análisis"',
    "bot-message"
  );*/
  chatPersistente.agregar(
    "bot",
    'Por favor, selecciona un candidato de la tabla haciendo clic en "✅ Seleccionar para Análisis"'
  );
}

// Función para mostrar resultados de IA
function mostrarResultadoIA(resultado) {
  const resultadoHTML = `
        <div class="candidate-analysis" style="border-left-color: #28a745;">
            <h5>🤖 Análisis IA Completo</h5>
            <div class="analysis-field">
                <strong>📊 Puntuación general:</strong> ${resultado.puntuacion || "N/A"
    }
            </div>
            <div class="analysis-field">
                <strong>💡 Fortalezas:</strong> ${resultado.fortalezas || "Por analizar"
    }
            </div>
            <div class="analysis-field">
                <strong>⚠️ Áreas de mejora:</strong> ${resultado.areas_mejora || "Por analizar"
    }
            </div>
            <div class="analysis-field">
                <strong>🎯 Recomendación:</strong> ${resultado.recomendacion || "Por analizar"
    }
            </div>
        </div>
    `;

  addHTMLMessage(resultadoHTML, "bot-message");

  // Mostrar confirmación después del análisis
  setTimeout(() => {
    mostrarConfirmacionAyuda();
  }, 1000);
}

// Función para mostrar confirmación de ayuda
function mostrarConfirmacionAyuda() {
  const confirmacionHTML = `
        <div class="chat-message bot-message">
            <p>¿Puedo ayudarte con algo más?</p>
            <div class="special-buttons" style="margin-top: 10px;">
                <button class="special-btn" onclick="manejarConfirmacion(true)" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    Sí
                </button>
                <button class="special-btn" onclick="manejarConfirmacion(false)" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                    No
                </button>
            </div>
        </div>
    `;

  addHTMLMessage(confirmacionHTML, "bot-message");
}

// Función para manejar la respuesta de confirmación
function manejarConfirmacion(respuesta) {
  if (respuesta) {
    // Si dice "Sí", mostrar las opciones principales
    addMessage("Sí", "user-message");
    mostrarOpcionesPrincipales();
  } else {
    // Si dice "No", mostrar mensaje de espera
    addMessage("No", "user-message");
    mostrarMensajeEspera();
  }
}

// Función para mostrar las opciones principales - VERSIÓN CORREGIDA
function mostrarOpcionesPrincipales() {
  // document.getElementById('manual-description-container').style.display = 'none';
  document.getElementById("analysis-input-container").style.display = "none";
  toggleSelectionButtons(false);
  toggleImproveButtons(false);

  // Ocultar también los contenedores del nuevo flujo
  if (document.getElementById("opciones-mejora-container")) {
    document.getElementById("opciones-mejora-container").style.display = "none";
  }
  if (document.getElementById("manual-description-container")) {
    document.getElementById("manual-description-container").style.display =
      "none";
  }

  const opcionesHTML = `
        <div class="special-buttons">
            <p>¡Perfecto! ¿En qué más puedo ayudarte?</p>
            <div class="special-buttons">
                <!--<button class="special-btn" onclick="mostrarAnalisisCandidato()">
                    🔍 Análisis de candidatos
                     🔍 Comparar CV con SAP
                </button>-->
                <button class="special-btn" onclick="activarComparacionCV()">
                    🔍 Análisis de candidatos
                </button>
                <button class="special-btn" onclick="mostrarOpcionesMejoraDescripcion()">
                    ✏️ Mejorar descripción de puesto
                </button>
                <!--
                <button class="special-btn" onclick="procesarSAPSSFF()">
                    📊 Procesar SAP SSFF
                </button>-->
            </div>
        </div>
    `;

  addHTMLMessage(opcionesHTML, "bot-message");
}

// JS DEMAS PARA mostrarOpcionesMejoraDescripcion

// Función para mostrar las opciones de mejora de descripción
function mostrarOpcionesMejoraDescripcion() {
  // Ocultar otros contenedores
  if (document.getElementById("analysis-input-container")) {
    document.getElementById("analysis-input-container").style.display = "none";
  }

  if (document.getElementById("manual-description-container")) {
    document.getElementById("manual-description-container").style.display =
      "none";
  }

  // Ocultar botones de selección en la tabla
  toggleSelectionButtons(false);
  toggleImproveButtons(false);

  // Agregar mensaje al chat
  addMessage("Quiero mejorar una descripción de puesto", "user-message");

  // Mostrar opciones después de un breve delay
  setTimeout(() => {
    const opcionesHTML = `
            <div class="special-buttons">
                <p>Selecciona el tipo de descripción que deseas mejorar:</p>
                <div class="special-buttons">
                    <button class="special-btn" onclick="seleccionarDescripcionManual()" 
                            style="background: #3ca6e5;">
                        📝 Descripción Manual
                    </button>
                    <!--
                    <button class="special-btn" onclick="seleccionarDescripcionATS()" 
                            style="background: #28a745;">
                        📊 Descripción desde ATS
                    </button>-->
                    <button class="special-btn" onclick="procesarSAPSSFF()"
                            style="background: #28a745;">
                        📊 Procesar SAP SSFF
                    </button>
                    <button class="special-btn" onclick="mostrarOpcionesPrincipales()" 
                            style="background: #6c757d;">
                        ↩️ Volver
                    </button>
                </div>
            </div>
        `;

    addHTMLMessage(opcionesHTML, "bot-message");
  }, 500);
}

// Función para seleccionar descripción manual
function seleccionarDescripcionManual() {
  addMessage("Descripción Manual", "user-message");

  // Ocultar botones de selección en la tabla
  toggleSelectionButtons(false);
  toggleImproveButtons(false);

  // Mostrar textarea para descripción manual usando la función existente
  setTimeout(() => {
    mostrarInputManualDescripcion();
  }, 500);
}

// Función para seleccionar descripción desde ATS
function seleccionarDescripcionATS() {
  addMessage("Descripción desde ATS", "user-message");

  // Ocultar otros contenedores
  if (document.getElementById("manual-description-container")) {
    document.getElementById("manual-description-container").style.display =
      "none";
  }

  // Usar la función existente de mejora de descripción
  setTimeout(() => {
    mejorarDescripcionPuesto();
  }, 500);
}

// Función auxiliar para mostrar botones de mejora en la tabla (si toggleImproveButtons no existe)
function mostrarBotonesMejoraEnTabla() {
  // Ocultar otros botones si están visibles
  if (typeof toggleSelectionButtons === "function") {
    toggleSelectionButtons(false);
  }

  // Mostrar botones de mejora
  const botonesMejora = document.querySelectorAll(".btn-improve-job");
  if (botonesMejora.length > 0) {
    botonesMejora.forEach((btn) => {
      btn.style.display = "inline-block";
    });

    addMessage(
      'Por favor, selecciona un candidato de la tabla haciendo clic en "✏️ Mejorar Descripción" para mejorar el puesto al que aplicó',
      "bot-message"
    );
  }
}

// Función para mostrar mensaje de espera
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

  addHTMLMessage(esperaHTML, "bot-message");
}

// Función auxiliar para agregar mensajes HTML (si no está definida en el main)
/*
function addHTMLMessage(html, type) {
  const chatBody = document.getElementById("chat-body");
  const div = document.createElement("div");
  div.classList.add("chat-message", type);
  div.innerHTML = html;
  chatBody.appendChild(div);
  chatBody.scrollTop = chatBody.scrollHeight;
}*/

function addHTMLMessage(html, type = "bot-message") {
  const tipo = type === "user-message" ? "user" : "bot";
  chatPersistente.agregar(tipo, "", html);
}

// Función auxiliar para agregar mensajes de texto (si no está definida en el main)
/*
function addMessage(message, type) {
  const chatBody = document.getElementById("chat-body");
  const div = document.createElement("div");
  div.classList.add("chat-message", type);
  div.textContent = message;
  chatBody.appendChild(div);
  chatBody.scrollTop = chatBody.scrollHeight;
}*/

// REEMPLAZA las funciones addMessage y addHTMLMessage existentes
function addMessage(text, type = "bot-message") {
  const tipo = type === "user-message" ? "user" : "bot";
  chatPersistente.agregar(tipo, text);
}

// NUEVO

// Función para seleccionar candidato para mejora de puesto
function seleccionarParaMejoraPuesto(id) {
  const candidato = candidatosData.find((c) => c.id_candidate == id);
  if (candidato) {
    // Ocultar todos los botones de mejora
    toggleImproveButtons(false);

    // Mostrar mensaje en el chat
    addMessage(
      `Seleccioné a ${candidato.nombre_candidate} ${candidato.apellidop_candidate
      } para mejorar la descripción del puesto: ${candidato.puesto || "Sin especificar"
      }`,
      "user-message"
    );

    // Iniciar función de mejora de descripción
    iniciarMejoraDescripcionPuesto(candidato);
  }
}

// FUNCIÓN ACTUALIZADA - Conectada a tu API real
async function iniciarMejoraDescripcionPuesto(candidato) {
  console.log("Candidato seleccionado para mejora de puesto:", candidato);

  // Mostrar información del puesto en el chat
  const mejoraHTML = `
        <div class="candidate-analysis">
            <h5>✏️ Mejorar Descripción de Puesto</h5>
            <div class="analysis-field">
                <strong>👤 Candidato:</strong> ${candidato.nombre_candidate} ${candidato.apellidop_candidate
    }
            </div>
            <div class="analysis-field">
                <strong>💼 Puesto actual:</strong> ${candidato.puesto || "No especificado"
    }
            </div>
            <div class="analysis-field">
                <strong>📧 Email:</strong> ${candidato.correo_candidate}
            </div>
            <div class="analysis-field">
                <strong>🔄 Proceso:</strong> Consultando con IA para generar descripción mejorada...
            </div>
        </div>
    `;

  addHTMLMessage(mejoraHTML, "bot-message");

  // Mostrar indicador de typing inicial
  const typingIndicator = mostrarTypingIndicator();

  setTimeout(async () => {
    ocultarTypingIndicator();

    // Mostrar mensaje de carga
    const loadingMessage = mostrarMensajeCarga(
      "Consultando con IA para generar descripción mejorada..."
    );

    try {
      // Actualizar estado de carga
      if (loadingMessage) {
        loadingMessage.querySelector(".loading-text").textContent =
          "Analizando puesto actual y generando recomendaciones...";
      }

      // LLAMADA REAL A TU API DE DJANGO
      const response = await fetch(`${API_BASE}/mejorar-descripcion/`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          candidato: {
            nombre: `${candidato.nombre_candidate} ${candidato.apellidop_candidate}`,
            email: candidato.correo_candidate,
            puesto: candidato.puesto,
          },
          puesto_actual: candidato.puesto || "Puesto no especificado",
        }),
      });

      if (!response.ok) {
        throw new Error(`Error HTTP: ${response.status}`);
      }

      // Actualizar estado de carga
      if (loadingMessage) {
        loadingMessage.querySelector(".loading-text").textContent =
          "Procesando respuesta de IA y formateando resultados...";
      }

      const data = await response.json();

      if (data.error) {
        throw new Error(data.error);
      }

      // Ocultar mensaje de carga
      ocultarMensajeCarga();

      // Mostrar la respuesta real de la IA
      mostrarDescripcionMejoradaReal(candidato, data.descripcion_mejorada);
    } catch (error) {
      console.error("Error al conectar con la API:", error);

      // Ocultar mensaje de carga
      ocultarMensajeCarga();

      // Mostrar error
      addMessage(
        `⚠️ Error: ${error.message}. El servicio de IA no está disponible en este momento. Por favor intenta más tarde.`,
        "bot-message"
      );

      setTimeout(() => {
        mostrarConfirmacionAyuda();
      }, 500);
    }
  }, 1500); // Pequeña pausa para el efecto typing
}

// FUNCIÓN ACTUALIZADA - Para mostrar respuesta real de la IA
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
            <button class="special-btn" onclick="copiarDescripcionIA('${descripcionIA
      .replace(/'/g, "\\'")
      .replace(
        /\n/g,
        "\\n"
      )}')" style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);">
                📋 Copiar Descripción
            </button>
            <button class="special-btn" onclick="personalizarDescripcion(${candidato.id_candidate
    })" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                🎨 Personalizar Más
            </button>
        </div>
    `;

  addHTMLMessage(descripcionMejoradaHTML, "bot-message");

  // Mostrar confirmación después de la mejora
  setTimeout(() => {
    mostrarConfirmacionAyuda();
  }, 1000);
}

// Función para copiar descripción IA real
function copiarDescripcionIA(descripcion) {
  const descripcionTexto = descripcion
    .replace(/\\n/g, "\n")
    .replace(/\\'/g, "'");

  navigator.clipboard
    .writeText(descripcionTexto)
    .then(() => {
      addMessage("✅ Descripción copiada al portapapeles", "bot-message");
    })
    .catch((err) => {
      console.error("Error al copiar: ", err);
      addMessage("❌ Error al copiar la descripción", "bot-message");
    });
}

// Función para personalizar más la descripción
function personalizarDescripcion(idCandidato) {
  const candidato = candidatosData.find((c) => c.id_candidate == idCandidato);
  addMessage(
    `Quiero personalizar más la descripción para ${candidato.nombre_candidate}`,
    "user-message"
  );

  setTimeout(() => {
    addHTMLMessage(
      `
            <div class="chat-message bot-message">
                <p>Para personalizar la descripción de ${candidato.puesto}, por favor especifica:</p>
                <ul>
                    <li>🔹 Nivel de experiencia requerido</li>
                    <li>🔹 Habilidades técnicas específicas</li>
                    <li>🔹 Beneficios adicionales</li>
                    <li>🔹 Modalidad de trabajo (presencial/remoto)</li>
                </ul>
                <p>Puedes escribir tus requerimientos en el chat.</p>
            </div>
        `,
      "bot-message"
    );
  }, 1000);
}

// MODIFICAR la función mejorarDescripcionPuesto existente:
function mejorarDescripcionPuesto() {
  addMessage("Quiero mejorar una descripción de puesto", "user-message");

  // Ocultar input de análisis de candidatos
  document.getElementById("analysis-input-container").style.display = "none";
  toggleSelectionButtons(false);

  // Mostrar botones de mejora en la tabla
  toggleImproveButtons(true);

  // Mensaje instructivo
  addMessage(
    'Por favor, selecciona un candidato de la tabla haciendo clic en "✏️ Mejorar Descripción" para mejorar el puesto al que aplicó',
    "bot-message"
  );
}

// FUNCIÓN DE FALLBACK (simulada - se usa si la API falla)
function mostrarDescripcionMejorada(candidato) {
  const puesto = candidato.puesto || "Puesto no especificado";

  const descripcionMejoradaHTML = `
        <div class="candidate-analysis" style="border-left: 4px solid #ff6b35;">
            <h5>🚀 Descripción Mejorada para: ${puesto}</h5>
            
            <div class="analysis-field">
                <strong>📋 Título Optimizado:</strong> Especialista en ${puesto} - Oportunidad de Crecimiento
            </div>
            
            <div class="analysis-field">
                <strong>🎯 Responsabilidades Clave:</strong>
                <ul style="margin: 5px 0; padding-left: 20px;">
                    <li>Gestión y coordinación de actividades del área</li>
                    <li>Desarrollo e implementación de estrategias</li>
                    <li>Supervisión de procesos y mejora continua</li>
                    <li>Colaboración interdepartamental</li>
                </ul>
            </div>
            
            <div class="analysis-field">
                <strong>✅ Requisitos Deseables:</strong>
                <ul style="margin: 5px 0; padding-left: 20px;">
                    <li>Experiencia comprobada en puesto similar</li>
                    <li>Habilidades de liderazgo y comunicación</li>
                    <li>Capacidad analítica y resolutiva</li>
                    <li>Orientación a resultados</li>
                </ul>
            </div>
            
            <div class="analysis-field">
                <strong>🌟 Beneficios Destacados:</strong>
                <ul style="margin: 5px 0; padding-left: 20px;">
                    <li>Plan de desarrollo profesional</li>
                    <li>Ambiente de trabajo colaborativo</li>
                    <li>Oportunidades de crecimiento</li>
                    <li>Paquete de beneficios competitivo</li>
                </ul>
            </div>
        </div>
        
        <div class="special-buttons" style="margin-top: 15px;">
            <button class="special-btn" onclick="copiarDescripcion('${puesto}')" style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);">
                📋 Copiar Descripción
            </button>
            <button class="special-btn" onclick="personalizarDescripcion(${candidato.id_candidate})" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                🎨 Personalizar Más
            </button>
        </div>
    `;

  addHTMLMessage(descripcionMejoradaHTML, "bot-message");
}

// Función para copiar descripción simulada
function copiarDescripcion(puesto) {
  const descripcion = `Descripción mejorada para: ${puesto}\n\nTítulo: Especialista en ${puesto} - Oportunidad de Crecimiento\n\nResponsabilidades:\n• Gestión y coordinación de actividades\n• Desarrollo de estrategias\n• Supervisión de procesos\n• Colaboración interdepartamental\n\nRequisitos:\n• Experiencia en puesto similar\n• Habilidades de liderazgo\n• Capacidad analítica\n• Orientación a resultados`;

  navigator.clipboard
    .writeText(descripcion)
    .then(() => {
      addMessage("✅ Descripción copiada al portapapeles", "bot-message");
    })
    .catch((err) => {
      console.error("Error al copiar: ", err);
      addMessage("❌ Error al copiar la descripción", "bot-message");
    });
}

// Función para personalizar más la descripción
function personalizarDescripcion(idCandidato) {
  const candidato = candidatosData.find((c) => c.id_candidate == idCandidato);
  addMessage(
    `Quiero personalizar más la descripción para ${candidato.nombre_candidate}`,
    "user-message"
  );

  setTimeout(() => {
    addHTMLMessage(
      `
            <div class="chat-message bot-message">
                <p>Para personalizar la descripción de "${candidato.puesto}", por favor especifica en el chat:</p>
                <ul>
                    <li>🔹 Nivel de experiencia requerido</li>
                    <li>🔹 Habilidades técnicas específicas</li>
                    <li>🔹 Beneficios adicionales</li>
                    <li>🔹 Modalidad de trabajo (presencial/remoto)</li>
                    <li>🔹 Cualquier otro requerimiento especial</li>
                </ul>
                <p>Escribe tus especificaciones y las enviaré a la IA para una personalización avanzada.</p>
            </div>
        `,
      "bot-message"
    );
  }, 1000);
}

async function enviarDescripcionParaMejora() {
  const descripcion = document
    .getElementById("descripcion-puesto-input")
    .value.trim();

  if (!descripcion) {
    Swal.fire(
      "⚠️",
      "Por favor ingresa la descripción del puesto antes de enviarla.",
      "warning"
    );
    return;
  }

  // Mostrar indicador de typing inicial
  const typingIndicator = mostrarTypingIndicator();

  setTimeout(async () => {
    ocultarTypingIndicator();

    // Mostrar mensaje de carga con animación
    const loadingMessage = mostrarMensajeCarga(
      "Enviando descripción a IA para mejorar..."
    );

    try {
      // Actualizar estado de carga
      if (loadingMessage) {
        loadingMessage.querySelector(".loading-text").textContent =
          "Analizando descripción actual y generando mejoras...";
      }

      const response = await fetch(`${API_BASE}/mejorar-descripcion/`, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          candidato: { nombre: "Manual Input", email: "", puesto: descripcion },
          puesto_actual: descripcion,
        }),
      });

      if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);

      // Actualizar estado de carga
      if (loadingMessage) {
        loadingMessage.querySelector(".loading-text").textContent =
          "Procesando respuesta de IA y formateando resultados...";
      }

      const data = await response.json();

      if (data.error) throw new Error(data.error);

      // Ocultar mensaje de carga
      ocultarMensajeCarga();

      // Mostrar resultado optimizado por IA
      addHTMLMessage(
        `
          <div class="chat-message bot-message">
            ✅ Descripción mejorada por IA:<br>
            ${data.descripcion_mejorada.replace(/\n/g, "<br>")}
          </div>
        `,
        "bot-message"
      );

      setTimeout(() => {
        mostrarConfirmacionAyuda();
      }, 500);
    } catch (error) {
      console.error("Error al conectar con la API:", error);

      // Ocultar mensaje de carga
      ocultarMensajeCarga();

      Swal.fire(
        "⚠️",
        "La IA no está disponible en este momento. Intenta más tarde.",
        "error"
      );

      setTimeout(() => {
        mostrarConfirmacionAyuda();
      }, 500);
    }
  }, 1500); // Pequeña pausa para el efecto typing
}

// DETALLES DEL MENSAJE DE ADVERTENCIA SOLO AQUI
// addMessage('⚠️ El servicio de IA no está disponible en este momento. Por favor intenta más tarde.', 'bot-message');
/*
        setTimeout(() => {
            mostrarDescripcionMejorada(candidato);
        }, 1000);*/

function mostrarInputManualDescripcion() {
  // Ocultar inputs de análisis y botones de mejora de candidatos
  document.getElementById("analysis-input-container").style.display = "none";
  toggleSelectionButtons(false);
  toggleImproveButtons(false);

  // Agregar el textarea y el botón **dentro del chat** cada vez
  const html = `
            <p>✏️ Ingresa la descripción del puesto que deseas mejorar:</p>
            <textarea id="descripcion-puesto-input" class="analysis-input" rows="4" placeholder="Escribe aquí la descripción del puesto que deseas mejorar..."></textarea>
            <button class="analysis-btn" onclick="enviarDescripcionParaMejora()">
                ✏️ Optimizar descripción con IA
            </button>
        
    `;
  addHTMLMessage(html, "bot-message");
}

// js/IA/analisisIA.js - FUNCIONES PARA SAP SSFF (VERSIÓN CORREGIDA)

// Variable global para almacenar los datos de puestos
let puestosData = [];

// Función para cargar los puestos desde el CSV
async function cargarPuestosDesdeCSV() {
  try {
    const response = await fetch("puestos.csv");
    const csvText = await response.text();
    puestosData = parsearCSVPuestos(csvText);
    return puestosData;
  } catch (error) {
    console.error("Error cargando puestos.csv:", error);
    return [];
  }
}

// Función para parsear el CSV de puestos
function parsearCSVPuestos(csvText) {
  const lineas = csvText.split("\n");
  const puestos = [];

  // Saltar la primera línea (headers)
  for (let i = 1; i < lineas.length; i++) {
    const linea = lineas[i].trim();
    if (!linea) continue;

    // Parsear considerando que hay HTML que puede contener comas
    const campos = parsearLineaCSV(linea);

    if (campos.length >= 5) {
      puestos.push({
        reqId: campos[0],
        titulo: campos[1],
        categoria: campos[2],
        ubicacion: campos[3],
        descripcion: campos[4],
      });
    }
  }
  return puestos;
}

// Función auxiliar para parsear líneas CSV con contenido HTML
function parsearLineaCSV(linea) {
  const campos = [];
  let campoActual = "";
  let entreComillas = false;

  for (let i = 0; i < linea.length; i++) {
    const char = linea[i];

    if (char === '"') {
      entreComillas = !entreComillas;
    } else if (char === "," && !entreComillas) {
      campos.push(campoActual);
      campoActual = "";
    } else {
      campoActual += char;
    }
  }

  campos.push(campoActual);
  return campos;
}

/////
// Función principal para procesar SAP SSFF - CON MODAL
async function procesarSAPSSFF() {
  console.log("🔍 procesarSAPSSFF ejecutándose");

  // Solo mensaje inicial en el chat
  addMessage("Quiero procesar un informe SAP SSFF", "user-message");
  addMessage("📊 Cargando puestos desde SAP SSFF...", "bot-message");

  try {
    // Cargar datos del CSV
    const puestos = await cargarPuestosDesdeCSV();

    if (puestos.length === 0) {
      addMessage(
        "❌ No se pudieron cargar los puestos desde el archivo CSV.",
        "bot-message"
      );
      return;
    }

    // Mostrar modal con la tabla de puestos
    mostrarModalPuestos(puestos);
  } catch (error) {
    console.error("Error en procesarSAPSSFF:", error);
    addMessage(
      "❌ Error al cargar los puestos SAP SSFF. Verifica que el archivo exista.",
      "bot-message"
    );
  }
}

// Función para mostrar el modal con la tabla de puestos
function mostrarModalPuestos(puestos) {
  // Crear el modal overlay
  const modalOverlay = document.createElement("div");
  modalOverlay.id = "sap-modal-overlay";
  modalOverlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 43, 69, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        padding: 20px;
    `;

  // Función para cerrar modal y mostrar confirmación
  const cerrarModal = () => {
    document.body.removeChild(modalOverlay);
    // Mostrar confirmación de ayuda después de cerrar
    setTimeout(() => {
      mostrarConfirmacionAyuda();
    }, 300);
  };

  // Crear el contenido del modal
  const modalContent = document.createElement("div");
  modalContent.style.cssText = `
        background: white;
        border-radius: 15px;
        padding: 30px;
        max-width: 90%;
        max-height: 90vh;
        width: 1200px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        display: flex;
        flex-direction: column;
    `;

  // Header del modal
  const modalHeader = document.createElement("div");
  modalHeader.style.cssText = `
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #002B45;
    `;

  const modalTitle = document.createElement("h3");
  modalTitle.textContent =
    "Selecciona un puesto para mejorar con IA";
  modalTitle.style.cssText = `
        margin: 0;
        color: #002B45;
        font-weight: 700;
    `;

  const closeButton = document.createElement("button");
  closeButton.textContent = "×";
  closeButton.style.cssText = `
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        font-size: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    `;
  closeButton.onclick = cerrarModal;

  modalHeader.appendChild(modalTitle);
  modalHeader.appendChild(closeButton);

  // AGREGAR FILTRO DE BÚSQUEDA
  const filterContainer = document.createElement("div");
  filterContainer.style.cssText = `
        margin-bottom: 20px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 10px;
        border: 1px solid #dee2e6;
    `;

  const filterRow = document.createElement("div");
  filterRow.style.cssText = `
        display: flex;
        gap: 15px;
        align-items: center;
    `;

  // Input de búsqueda
  const searchInput = document.createElement("input");
  searchInput.type = "text";
  searchInput.placeholder = "🔍 Buscar por ID, puesto o descripción...";
  searchInput.style.cssText = `
        flex: 1;
        padding: 12px 15px;
        border: 2px solid #002B45;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: all 0.3s ease;
    `;

  // Selector de categoría
  const categorySelect = document.createElement("select");
  categorySelect.style.cssText = `
        padding: 12px 15px;
        border: 2px solid #002B45;
        border-radius: 8px;
        font-size: 14px;
        background: white;
        cursor: pointer;
        min-width: 180px;
    `;

  // Opciones de categoría
  const allOption = document.createElement("option");
  allOption.value = "";
  allOption.textContent = "Todas las categorías";
  categorySelect.appendChild(allOption);

  // Obtener categorías únicas
  const categoriasUnicas = [...new Set(puestos.map((p) => p.categoria))];
  categoriasUnicas.forEach((categoria) => {
    const option = document.createElement("option");
    option.value = categoria;
    option.textContent = categoria;
    categorySelect.appendChild(option);
  });

  // Botón para limpiar filtros
  const clearButton = document.createElement("button");
  clearButton.textContent = "Limpiar filtros";
  clearButton.style.cssText = `
        padding: 12px 20px;
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.3s ease;
    `;
  clearButton.addEventListener("mouseenter", () => {
    clearButton.style.background = "#5a6268";
  });
  clearButton.addEventListener("mouseleave", () => {
    clearButton.style.background = "#6c757d";
  });

  filterRow.appendChild(searchInput);
  filterRow.appendChild(categorySelect);
  filterRow.appendChild(clearButton);
  filterContainer.appendChild(filterRow);

  // Contador de resultados
  const resultCount = document.createElement("div");
  resultCount.id = "sap-result-count";
  resultCount.style.cssText = `
        text-align: right;
        margin-bottom: 10px;
        color: #6c757d;
        font-size: 13px;
        font-weight: 500;
    `;
  resultCount.textContent = `${puestos.length} puestos encontrados`;

  // Contenedor de la tabla
  const tableContainer = document.createElement("div");
  tableContainer.style.cssText = `
        flex: 1;
        overflow-y: auto;
        margin-bottom: 20px;
        max-height: 400px;
    `;

  // Crear tabla
  const table = document.createElement("table");
  table.id = "sap-puestos-table";
  table.className = "table table-striped table-hover";
  table.style.cssText = `
        width: 100%;
        font-size: 14px;
    `;

  // Header de la tabla
  const thead = document.createElement("thead");
  thead.style.background = "#002B45";
  thead.innerHTML = `
        <tr>
            <th style="color: white; padding: 12px; width: 100px;">ID</th>
            <th style="color: white; padding: 12px;">Puesto</th>
            <th style="color: white; padding: 12px; width: 150px;">Categoría</th>
            <th style="color: white; padding: 12px; width: 200px;">Ubicación</th>
            <th style="color: white; padding: 12px; width: 120px;">Acción</th>
        </tr>
    `;

  // Body de la tabla
  const tbody = document.createElement("tbody");
  tbody.id = "sap-puestos-body";

  // Función para renderizar filas
  function renderRows(puestosToRender) {
    tbody.innerHTML = "";
    puestosToRender.forEach((puesto, index) => {
      const row = document.createElement("tr");
      row.style.cursor = "pointer";
      row.onmouseenter = () => (row.style.backgroundColor = "#f8f9fa");
      row.onmouseleave = () => (row.style.backgroundColor = "");

      row.innerHTML = `
            <td style="padding: 10px; font-weight: bold; color: #002B45;">${puesto.reqId
        }</td>
            <td style="padding: 10px;">
                <strong>${puesto.titulo}</strong>
                ${puesto.descripcion
          ? `<br><small class="text-muted">${limpiarHTML(
            puesto.descripcion
          ).substring(0, 100)}...</small>`
          : ""
        }
            </td>
            <td style="padding: 10px;">
                <span class="badge badge-categoria" style="background: #17a2b8; color: white; padding: 4px 8px; border-radius: 4px;">
                    ${puesto.categoria}
                </span>
            </td>
            <td style="padding: 10px;">${puesto.ubicacion}</td>
            <td style="padding: 10px;">
                <button class="btn btn-success btn-sm" 
                        onclick="seleccionarPuestoDesdeModal('${puesto.reqId}')"
                        style="padding: 6px 12px; font-size: 12px; width: 100%;">
                    Optimizar
                </button>
            </td>
        `;
      tbody.appendChild(row);
    });

    // Actualizar contador de resultados
    resultCount.textContent = `${puestosToRender.length} de ${puestos.length} puestos encontrados`;
  }

  // Función para filtrar la tabla
  function filterTable() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedCategory = categorySelect.value.toLowerCase();

    const filtered = puestos.filter((puesto) => {
      const matchSearch =
        searchTerm === "" ||
        puesto.reqId.toLowerCase().includes(searchTerm) ||
        puesto.titulo.toLowerCase().includes(searchTerm) ||
        (puesto.descripcion &&
          puesto.descripcion.toLowerCase().includes(searchTerm));

      const matchCategory =
        selectedCategory === "" ||
        puesto.categoria.toLowerCase().includes(selectedCategory);

      return matchSearch && matchCategory;
    });

    renderRows(filtered);

    // Mostrar mensaje si no hay resultados
    if (filtered.length === 0) {
      const noResultsRow = document.createElement("tr");
      noResultsRow.innerHTML = `
        <td colspan="5" style="text-align: center; padding: 30px; color: #6c757d;">
          <div style="font-size: 16px; margin-bottom: 10px;">😕 No se encontraron puestos</div>
          <small>Intenta con otros términos de búsqueda o cambia la categoría</small>
        </td>
      `;
      tbody.appendChild(noResultsRow);
    }
  }

  // Configurar eventos de los filtros
  searchInput.addEventListener("input", filterTable);
  categorySelect.addEventListener("change", filterTable);
  clearButton.onclick = () => {
    searchInput.value = "";
    categorySelect.value = "";
    filterTable();
  };

  // Renderizar todas las filas inicialmente
  renderRows(puestos);

  table.appendChild(thead);
  table.appendChild(tbody);
  tableContainer.appendChild(table);

  // Footer del modal
  const modalFooter = document.createElement("div");
  modalFooter.style.cssText = `
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #dee2e6;
        gap: 10px;
    `;

  const counter = document.createElement("div");
  counter.innerHTML = `<strong>${puestos.length}</strong> puestos disponibles en SAP SSFF`;
  counter.style.color = "#6c757d";

  const cancelButton = document.createElement("button");
  cancelButton.textContent = "Cancelar";
  cancelButton.className = "btn btn-secondary";
  cancelButton.onclick = cerrarModal;

  modalFooter.appendChild(counter);
  modalFooter.appendChild(cancelButton);

  // Ensamblar el modal
  modalContent.appendChild(modalHeader);
  modalContent.appendChild(filterContainer);
  modalContent.appendChild(resultCount);
  modalContent.appendChild(tableContainer);
  modalContent.appendChild(modalFooter);
  modalOverlay.appendChild(modalContent);

  // Agregar al body
  document.body.appendChild(modalOverlay);

  // Cerrar modal al hacer click fuera del contenido
  modalOverlay.onclick = (e) => {
    if (e.target === modalOverlay) {
      cerrarModal();
    }
  };

  // Enfocar el input de búsqueda automáticamente
  setTimeout(() => {
    searchInput.focus();
  }, 300);
}

// Función para seleccionar puesto desde el modal
function seleccionarPuestoDesdeModal(reqId) {
  // Cerrar el modal
  const modal = document.getElementById("sap-modal-overlay");
  if (modal) {
    document.body.removeChild(modal);
  }

  // Buscar el puesto seleccionado
  const puesto = puestosData.find((p) => p.reqId === reqId);
  if (!puesto) return;

  // Mostrar mensaje en el chat
  addMessage(
    `Seleccioné el puesto: ${puesto.titulo} (${puesto.reqId}) para mejorar con IA`,
    "user-message"
  );

  // Iniciar proceso de mejora con IA
  iniciarMejoraPuestoSAP(puesto);
}

// Función para mejorar un puesto SAP con IA
async function iniciarMejoraPuestoSAP(puesto) {
  // Mostrar información del puesto seleccionado
  const infoPuestoHTML = `
    <div class="candidate-analysis">
      <h5>🚀 Mejorando Puesto SAP: ${puesto.titulo}</h5>
      <div class="analysis-field">
        <strong>📋 ID Requisición:</strong> ${puesto.reqId}
      </div>
      <div class="analysis-field">
        <strong>🏷️ Categoría:</strong> ${puesto.categoria}
      </div>
      <div class="analysis-field">
        <strong>📍 Ubicación:</strong> ${puesto.ubicacion}
      </div>
      <div class="analysis-field">
        <strong>🔄 Proceso:</strong> Consultando con IA para optimizar descripción...
      </div>
    </div>
  `;

  addHTMLMessage(infoPuestoHTML, "bot-message");

  // Mostrar indicador de typing inicial
  const typingIndicator = mostrarTypingIndicator();

  setTimeout(async () => {
    ocultarTypingIndicator();

    // Mostrar mensaje de carga
    const loadingMessage = mostrarMensajeCarga(
      "Consultando con IA para optimizar descripción..."
    );

    try {
      // Actualizar estado de carga
      if (loadingMessage) {
        loadingMessage.querySelector(".loading-text").textContent =
          "Analizando descripción del puesto SAP...";
      }

      // LLAMADA DIRECTA A TU ENDPOINT DE DJANGO
      const response = await fetch(
        `${API_BASE}/mejorar-puesto/${puesto.reqId}/?mejorar=1`
      );

      if (!response.ok) {
        throw new Error(`Error HTTP: ${response.status}`);
      }

      // Actualizar estado de carga
      if (loadingMessage) {
        loadingMessage.querySelector(".loading-text").textContent =
          "Procesando optimizaciones de IA...";
      }

      const data = await response.json();

      if (data.error) {
        throw new Error(data.error);
      }

      // Ocultar mensaje de carga
      ocultarMensajeCarga();

      // Mostrar resultados de tu función Python
      mostrarResultadoMejoraPuesto(puesto, data);
    } catch (error) {
      console.error("Error mejorando puesto SAP:", error);

      // Ocultar mensaje de carga
      ocultarMensajeCarga();

      addMessage(
        "❌ Error al conectar con el servicio de IA. Intenta más tarde.",
        "bot-message"
      );

      // Mostrar confirmación de ayuda
      setTimeout(() => {
        mostrarConfirmacionAyuda();
      }, 1000);
    }
  }, 1500); // Pequeña pausa para el efecto typing
}

// Función para mostrar los resultados de la mejora
function mostrarResultadoMejoraPuesto(puesto, resultado) {
  const resultadoHTML = `
        <div class="candidate-analysis" style="border-left: 4px solid #002B45;">
            <h5>✅ Puesto Optimizado: ${puesto.titulo}</h5>
            
            ${resultado.descripcion_original
      ? `
            <div class="analysis-field">
                <strong>📝 Descripción Original:</strong>
                <div style="background: #fff3cd; padding: 10px; border-radius: 8px; margin-top: 5px; font-size: 13px; max-height: 150px; overflow-y: auto;">
                    ${limpiarHTML(resultado.descripcion_original).substring(
        0,
        300
      )}...
                </div>
            </div>
            `
      : ""
    }
            
            <div class="analysis-field">
                <strong>🚀 Descripción Mejorada por IA:</strong>
                <div style="background: #d4edda; padding: 15px; border-radius: 10px; margin-top: 10px; white-space: pre-wrap; font-size: 14px; line-height: 1.5;">
                    ${resultado.descripcion_mejorada ||
    "No se pudo generar la descripción mejorada"
    }
                </div>
            </div>
        </div>
        
        <div class="special-buttons" style="margin-top: 15px;">
            <button class="special-btn" onclick="copiarDescripcionPuesto('${(
      resultado.descripcion_mejorada || ""
    )
      .replace(/'/g, "\\'")
      .replace(/\n/g, "\\n")}')" 
                    style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);">
                📋 Copiar Descripción Mejorada
            </button>
            <button class="special-btn" onclick="procesarOtroPuesto()" 
                    style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                📊 Procesar Otro Puesto
            </button>
        </div>
    `;

  addHTMLMessage(resultadoHTML, "bot-message");

  // Mostrar confirmación de ayuda
  setTimeout(() => {
    mostrarConfirmacionAyuda();
  }, 1000);
}

// Función auxiliar para limpiar HTML (opcional)
function limpiarHTML(html) {
  return html
    .replace(/<[^>]*>/g, " ")
    .replace(/\s+/g, " ")
    .trim();
}

// Función para copiar la descripción del puesto
function copiarDescripcionPuesto(descripcion) {
  const descripcionTexto = descripcion
    .replace(/\\n/g, "\n")
    .replace(/\\'/g, "'");

  navigator.clipboard
    .writeText(descripcionTexto)
    .then(() => {
      addMessage(
        "✅ Descripción mejorada copiada al portapapeles",
        "bot-message"
      );
    })
    .catch((err) => {
      console.error("Error al copiar:", err);
      addMessage("❌ Error al copiar la descripción", "bot-message");
    });
}

// Función para procesar otro puesto
function procesarOtroPuesto() {
  addMessage("Quiero procesar otro puesto SAP", "user-message");
  procesarSAPSSFF();
}

// js/IA/analisisIA.js - FUNCIONALIDAD DE COMPARACIÓN CV vs SAP

// Función para comparar CV con SAP
function compararCVConSAP(candidatoId) {
  const candidato = candidatosData.find((c) => c.id_candidate == candidatoId);
  if (!candidato) return;

  // Ocultar botones de comparación
  toggleComparisonButtons(false);

  // Mostrar mensaje en el chat
  addMessage(
    `Quiero comparar el CV de ${candidato.nombre_candidate} ${candidato.apellidop_candidate} con un puesto SAP`,
    "user-message"
  );

  // Mostrar modal de puestos para seleccionar cuál comparar
  mostrarModalPuestosParaComparacion(candidato);
}

// Función para mostrar modal de puestos específico para comparación
async function mostrarModalPuestosParaComparacion(candidato) {
  try {
    const puestos = await cargarPuestosDesdeCSV();

    if (puestos.length === 0) {
      addMessage(
        "❌ No se pudieron cargar los puestos SAP para comparación.",
        "bot-message"
      );
      return;
    }

    // Crear el modal overlay para comparación
    const modalOverlay = document.createElement("div");
    modalOverlay.id = "comparacion-modal-overlay";
    modalOverlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 43, 69, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
            padding: 20px;
        `;

    // Función para cerrar modal y mostrar confirmación
    const cerrarModal = () => {
      document.body.removeChild(modalOverlay);
      setTimeout(() => {
        mostrarConfirmacionAyuda();
      }, 300);
    };

    // Crear contenido del modal
    const modalContent = document.createElement("div");
    modalContent.style.cssText = `
            background: white;
            border-radius: 15px;
            padding: 30px;
            max-width: 90%;
            max-height: 90vh;
            width: 1200px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            display: flex;
            flex-direction: column;
        `;

    // Header del modal
    const modalHeader = document.createElement("div");
    modalHeader.style.cssText = `
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #002B45;
        `;

    const modalTitle = document.createElement("h3");
    modalTitle.innerHTML = `Comparar CV de <span style="color: #002B45;">${candidato.nombre_candidate} ${candidato.apellidop_candidate}</span> con otros puestos`;
    modalTitle.style.cssText = `
            margin: 0;
            color: #333;
            font-weight: 700;
            font-size: 1.4rem;
        `;

    const closeButton = document.createElement("button");
    closeButton.textContent = "×";
    closeButton.style.cssText = `
            */background: #dc3545;/*
            color: black;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
    closeButton.onclick = cerrarModal;

    modalHeader.appendChild(modalTitle);
    modalHeader.appendChild(closeButton);

    // AGREGAR FILTRO DE BÚSQUEDA
    const filterContainer = document.createElement("div");
    filterContainer.style.cssText = `
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #dee2e6;
        `;

    const filterRow = document.createElement("div");
    filterRow.style.cssText = `
            display: flex;
            gap: 15px;
            align-items: center;
        `;

    // Input de búsqueda
    const searchInput = document.createElement("input");
    searchInput.type = "text";
    searchInput.placeholder = "Buscar por ID o nombre del puesto...";
    searchInput.style.cssText = `
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #002B45;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        `;

    // Selector de categoría
    const categorySelect = document.createElement("select");
    categorySelect.style.cssText = `
            padding: 12px 15px;
            border: 2px solid #002B45;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
            min-width: 180px;
        `;

    // Opciones de categoría
    const allOption = document.createElement("option");
    allOption.value = "";
    allOption.textContent = "Todas las categorías";
    categorySelect.appendChild(allOption);

    // Obtener categorías únicas
    const categoriasUnicas = [...new Set(puestos.map((p) => p.categoria))];
    categoriasUnicas.forEach((categoria) => {
      const option = document.createElement("option");
      option.value = categoria;
      option.textContent = categoria;
      categorySelect.appendChild(option);
    });

    // Botón para limpiar filtros
    const clearButton = document.createElement("button");
    clearButton.textContent = "Limpiar filtros";
    clearButton.style.cssText = `
            padding: 12px 20px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.3s ease;
        `;
    clearButton.addEventListener("mouseenter", () => {
      clearButton.style.background = "#5a6268";
    });
    clearButton.addEventListener("mouseleave", () => {
      clearButton.style.background = "#6c757d";
    });

    filterRow.appendChild(searchInput);
    filterRow.appendChild(categorySelect);
    filterRow.appendChild(clearButton);
    filterContainer.appendChild(filterRow);

    // Contador de resultados - DECLARARLO ANTES DE renderRows
    const resultCount = document.createElement("div");
    resultCount.style.cssText = `
            text-align: right;
            margin-bottom: 10px;
            color: #6c757d;
            font-size: 13px;
        `;
    resultCount.textContent = `${puestos.length} puestos`;

    // Contenedor de la tabla
    const tableContainer = document.createElement("div");
    tableContainer.style.cssText = `
            flex: 1;
            overflow-y: auto;
            margin-bottom: 20px;
            max-height: 400px;
        `;

    // Crear tabla
    const table = document.createElement("table");
    table.id = "puestos-comparacion-table";
    table.className = "table table-striped table-hover";
    table.style.cssText = `
            width: 100%;
            font-size: 14px;
        `;

    // Header de la tabla
    const thead = document.createElement("thead");
    thead.style.background = "#002B45";
    thead.innerHTML = `
            <tr>
                <th style="color: white; padding: 12px; width: 100px;">ID</th>
                <th style="color: white; padding: 12px;">Puesto</th>
                <th style="color: white; padding: 12px; width: 150px;">Categoría</th>
                <th style="color: white; padding: 12px; width: 200px;">Ubicación</th>
                <th style="color: white; padding: 12px; width: 120px;">Acción</th>
            </tr>
        `;

    // Body de la tabla
    const tbody = document.createElement("tbody");
    tbody.id = "puestos-comparacion-body";

    // Función para renderizar filas
    function renderRows(puestosToRender) {
      tbody.innerHTML = "";
      puestosToRender.forEach((puesto, index) => {
        const row = document.createElement("tr");
        row.style.cursor = "pointer";
        row.onmouseenter = () => (row.style.backgroundColor = "#f8f9fa");
        row.onmouseleave = () => (row.style.backgroundColor = "");

        row.innerHTML = `
                    <td style="padding: 10px; font-weight: bold; color: #002B45;">${puesto.reqId
          }</td>
                    <td style="padding: 10px;">
                        <strong>${puesto.titulo}</strong>
                        ${puesto.descripcion
            ? `<br><small class="text-muted">${limpiarHTML(
              puesto.descripcion
            ).substring(0, 100)}...</small>`
            : ""
          }
                    </td>
                    <td style="padding: 10px;">
                        <span class="badge badge-categoria" style="background: #17a2b8; color: white; padding: 4px 8px; border-radius: 4px;">
                            ${puesto.categoria}
                        </span>
                    </td>
                    <td style="padding: 10px;">${puesto.ubicacion}</td>
                    <td style="padding: 10px;">
                        <button class="btn btn-primary btn-sm" 
                                onclick="iniciarComparacionCV('${candidato.id_candidate
          }', '${puesto.reqId}')"
                                style="padding: 6px 12px; font-size: 12px; width: 100%;">
                            Compatibilidad
                        </button>
                    </td>
                `;
        tbody.appendChild(row);
      });

      // Actualizar contador de resultados
      resultCount.textContent = `${puestosToRender.length} de ${puestos.length} puestos`;
    }

    // Función para filtrar la tabla
    function filterTable() {
      const searchTerm = searchInput.value.toLowerCase();
      const selectedCategory = categorySelect.value.toLowerCase();

      const filtered = puestos.filter((puesto) => {
        const matchSearch =
          searchTerm === "" ||
          puesto.reqId.toLowerCase().includes(searchTerm) ||
          puesto.titulo.toLowerCase().includes(searchTerm) ||
          (puesto.descripcion &&
            puesto.descripcion.toLowerCase().includes(searchTerm));

        const matchCategory =
          selectedCategory === "" ||
          puesto.categoria.toLowerCase().includes(selectedCategory);

        return matchSearch && matchCategory;
      });

      renderRows(filtered);
    }

    // Configurar eventos de los filtros
    searchInput.addEventListener("input", filterTable);
    categorySelect.addEventListener("change", filterTable);
    clearButton.onclick = () => {
      searchInput.value = "";
      categorySelect.value = "";
      filterTable();
    };

    // Renderizar todas las filas inicialmente
    renderRows(puestos);

    table.appendChild(thead);
    table.appendChild(tbody);
    tableContainer.appendChild(table);

    // Footer del modal
    const modalFooter = document.createElement("div");
    modalFooter.style.cssText = `
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        `;

    const counter = document.createElement("div");
    counter.innerHTML = `<strong>${puestos.length}</strong> puestos disponibles para comparación`;
    counter.style.color = "#6c757d";

    const cancelButton = document.createElement("button");
    cancelButton.textContent = "Cancelar";
    cancelButton.className = "btn btn-secondary";
    cancelButton.onclick = cerrarModal;

    // NUEVO BOTÓN DE COMPARATIVA MANUAL
    //const manualButton = document.createElement("button");
    //manualButton.textContent = "📝 Comparativa Manual";
    //manualButton.className = "btn btn-info";
    //manualButton.style.background =
    //"linear-gradient(135deg, #17a2b8 0%, #20c997 100%)";
    //manualButton.style.border = "none";
    //manualButton.onclick = () => {
    //  document.body.removeChild(modalOverlay);
    //mostrarComparativaManual(candidato.id_candidate);
    //};

    modalFooter.appendChild(counter);
    //modalFooter.appendChild(manualButton);
    modalFooter.appendChild(cancelButton);

    // Ensamblar el modal
    modalContent.appendChild(modalHeader);
    modalContent.appendChild(filterContainer);
    modalContent.appendChild(resultCount); // AÑADIR ESTO ANTES DE LA TABLA
    modalContent.appendChild(tableContainer);
    modalContent.appendChild(modalFooter);
    modalOverlay.appendChild(modalContent);

    // Agregar al body
    document.body.appendChild(modalOverlay);

    // Cerrar modal al hacer click fuera del contenido
    modalOverlay.onclick = (e) => {
      if (e.target === modalOverlay) {
        cerrarModal();
      }
    };

    // Enfocar el input de búsqueda automáticamente
    setTimeout(() => {
      searchInput.focus();
    }, 300);
  } catch (error) {
    console.error("Error en modal de comparación:", error);
    addMessage(
      "❌ Error al cargar los puestos para comparación.",
      "bot-message"
    );
  }
}

// Función para iniciar la comparación CV vs SAP
async function iniciarComparacionCV(candidatoId, reqId) {
  // Cerrar el modal
  const modal = document.getElementById("comparacion-modal-overlay");
  if (modal) {
    document.body.removeChild(modal);
  }

  const candidato = candidatosData.find((c) => c.id_candidate == candidatoId);
  const puesto = puestosData.find((p) => p.reqId === reqId);

  if (!candidato || !puesto) return;

  // Mostrar mensaje en el chat
  addMessage(
    `Comparando CV de ${candidato.nombre_candidate} con puesto: ${puesto.titulo} (${reqId})`,
    "user-message"
  );

  // Mostrar información de la comparación
  const comparacionHTML = `
    <div class="candidate-analysis">
      <h5>🔍 Comparando CV con Puesto SAP</h5>
      <div class="analysis-field">
        <strong>👤 Candidato:</strong> ${candidato.nombre_candidate} ${candidato.apellidop_candidate}
      </div>
      <div class="analysis-field">
        <strong>💼 Puesto SAP:</strong> ${puesto.titulo} (${reqId})
      </div>
      <div class="analysis-field">
        <strong>🔄 Proceso:</strong> Analizando compatibilidad con IA...
      </div>
    </div>
  `;

  addHTMLMessage(comparacionHTML, "bot-message");

  // Mostrar indicador de typing inicial
  const typingIndicator = mostrarTypingIndicator();

  setTimeout(async () => {
    ocultarTypingIndicator();

    // Mostrar mensaje de carga
    const loadingMessage = mostrarMensajeCarga(
      "Analizando compatibilidad con IA..."
    );

    try {
      // Actualizar estado de carga
      if (loadingMessage) {
        loadingMessage.querySelector(".loading-text").textContent =
          "Extrayendo información del CV y descripción del puesto...";
      }

      // Llamar a tu endpoint de comparación
      const response = await fetch(
        `${API_BASE}/comparar/${candidatoId}/${reqId}/`
      );

      if (!response.ok) {
        throw new Error(`Error HTTP: ${response.status}`);
      }

      // Actualizar estado de carga
      if (loadingMessage) {
        loadingMessage.querySelector(".loading-text").textContent =
          "Calculando compatibilidad y generando recomendaciones...";
      }

      const data = await response.json();

      if (data.error) {
        throw new Error(data.error);
      }

      // Ocultar mensaje de carga
      ocultarMensajeCarga();

      // Mostrar resultados de la comparación
      mostrarResultadoComparacion(candidato, puesto, data);
    } catch (error) {
      console.error("Error en comparación CV:", error);

      // Ocultar mensaje de carga
      ocultarMensajeCarga();

      addMessage(
        "❌ Error al comparar el CV con el puesto SAP.",
        "bot-message"
      );

      setTimeout(() => {
        mostrarConfirmacionAyuda();
      }, 1000);
    }
  }, 1500); // Pequeña pausa para el efecto typing
}

// Función para mostrar resultados de la comparación
function mostrarResultadoComparacion(candidato, puesto, resultado) {
  const score = resultado.score || 0;
  const scoreColor =
    score >= 80 ? "#28a745" : score >= 60 ? "#ffc107" : "#dc3545";
  const scoreText =
    score >= 80
      ? "Alta compatibilidad"
      : score >= 60
        ? "Compatibilidad media"
        : "Baja compatibilidad";

  const resultadoHTML = `
        <div class="candidate-analysis" style="border-left: 4px solid ${scoreColor};">
            <h5>📊 Resultado de Comparación</h5>
            
            <div class="analysis-field">
                <strong>👤 Candidato:</strong> ${candidato.nombre_candidate} ${candidato.apellidop_candidate
    }
            </div>
            <div class="analysis-field">
                <strong>💼 Puesto:</strong> ${puesto.titulo}
            </div>
            
            <div class="analysis-field">
                <strong>🎯 Puntuación de Compatibilidad:</strong>
                <div style="background: ${scoreColor}; color: white; padding: 10px; border-radius: 8px; text-align: center; margin-top: 5px; font-weight: bold; font-size: 18px;">
                    ${score}/100 - ${scoreText}
                </div>
            </div>
            
            ${resultado.fortalezas
      ? `
            <div class="analysis-field">
                <strong>✅ Fortalezas:</strong>
                <div style="background: #d4edda; padding: 10px; border-radius: 8px; margin-top: 5px;">
                    ${resultado.fortalezas}
                </div>
            </div>
            `
      : ""
    }
            
            ${resultado.debilidades
      ? `
            <div class="analysis-field">
                <strong>⚠️ Áreas de Mejora:</strong>
                <div style="background: #fff3cd; padding: 10px; border-radius: 8px; margin-top: 5px;">
                    ${resultado.debilidades}
                </div>
            </div>
            `
      : ""
    }
            
            ${resultado.resumen
      ? `
            <div class="analysis-field">
                <strong>📋 Resumen:</strong>
                <div style="background: #e8f4fd; padding: 10px; border-radius: 8px; margin-top: 5px;">
                    ${resultado.resumen}
                </div>
            </div>
            `
      : ""
    }
            
            ${resultado.resultado
      ? `
            <div class="analysis-field">
                <strong>📝 Análisis Detallado:</strong>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin-top: 10px; white-space: pre-wrap; font-size: 14px; line-height: 1.5;">
                    ${resultado.resultado}
                </div>
            </div>
            `
      : ""
    }
        </div>
        
        <div class="special-buttons" style="margin-top: 15px;">
            <button class="special-btn" onclick="compararOtroCV(${candidato.id_candidate
    })" 
                    style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);">
                🔍 Comparar con Otro Puesto
            </button>
            <button class="special-btn" onclick="mostrarOpcionesPrincipales()" 
                    style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                🏠 Volver al Inicio
            </button>
        </div>
    `;

  addHTMLMessage(resultadoHTML, "bot-message");

  setTimeout(() => {
    mostrarConfirmacionAyuda();
  }, 1000);
}

// Función para comparar otro CV
function compararOtroCV(candidatoId) {
  addMessage("Quiero ver la compatibilidad con otro puesto SAP", "user-message");
  const candidato = candidatosData.find((c) => c.id_candidate == candidatoId);
  if (candidato) {
    mostrarModalPuestosParaComparacion(candidato);
  }
}

// Función para activar la comparación desde el chat
function activarComparacionCV() {
  addMessage("Quiero ver la compatibildad del CV con un puesto SAP", "user-message");

  // Ocultar otros inputs
  document.getElementById("analysis-input-container").style.display = "none";
  document.getElementById("manual-description-container").style.display =
    "none";
  toggleSelectionButtons(false);
  toggleImproveButtons(false);

  // Mostrar botones de comparación en la tabla
  toggleComparisonButtons(true);

  addMessage(
    'Por favor, selecciona un candidato de la tabla haciendo clic en "Ver compatibilidad"',
    "bot-message"
  );
}

// COMPARATIVA MANUAL

// Función para mostrar modal de comparativa manual
function mostrarComparativaManual(candidatoId) {
  const candidato = candidatosData.find((c) => c.id_candidate == candidatoId);
  if (!candidato) return;

  // Crear modal overlay
  const modalOverlay = document.createElement("div");
  modalOverlay.id = "comparativa-manual-overlay";
  modalOverlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 43, 69, 0.95);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        padding: 20px;
    `;

  // Contenido del modal
  const modalContent = document.createElement("div");
  modalContent.style.cssText = `
        background: white;
        border-radius: 15px;
        padding: 30px;
        max-width: 800px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    `;

  modalContent.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #002B45;">
            <h3 style="margin: 0; color: #002B45; font-weight: 700;">
                📝 Comparativa Manual - ${candidato.nombre_candidate} ${candidato.apellidop_candidate
    }
            </h3>
            <button onclick="cerrarComparativaManual()" style="background: #dc3545; color: white; border: none; border-radius: 50%; width: 40px; height: 40px; font-size: 24px; cursor: pointer;">
                ×
            </button>
        </div>

        <div style="margin-bottom: 20px;">
            <p><strong>👤 Candidato:</strong> ${candidato.nombre_candidate} ${candidato.apellidop_candidate
    }</p>
            <p><strong>📧 Email:</strong> ${candidato.correo_candidate}</p>
            <p><strong>💼 Puesto aplicado:</strong> ${candidato.puesto || "No especificado"
    }</p>
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #002B45;">
                📋 Descripción de la vacante (manual):
            </label>
            <textarea 
                id="descripcion-vacante-manual" 
                rows="8" 
                placeholder="Escribe aquí la descripción completa del puesto, requisitos, responsabilidades, habilidades requeridas..."
                style="width: 100%; padding: 15px; border: 2px solid #e9ecef; border-radius: 10px; font-size: 14px; resize: vertical; font-family: inherit;"
            ></textarea>
            <small style="color: #6c757d; display: block; margin-top: 5px;">
                ⓘ Describe detalladamente el puesto para una comparación más precisa con el CV del candidato.
            </small>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; gap: 15px;">
            <button onclick="cerrarComparativaManual()" class="btn btn-secondary" style="flex: 1;">
                ← Cancelar
            </button>
            <button onclick="enviarComparativaManual(${candidatoId})" class="btn btn-primary" style="flex: 2; background: linear-gradient(135deg, #002B45 0%, #3ca6e5 100%); border: none;">
                🔍 Comparar con IA
            </button>
        </div>
    `;

  modalOverlay.appendChild(modalContent);
  document.body.appendChild(modalOverlay);

  // Cerrar modal al hacer click fuera
  modalOverlay.onclick = (e) => {
    if (e.target === modalOverlay) {
      cerrarComparativaManual();
    }
  };
}

// Función para cerrar el modal
function cerrarComparativaManual() {
  const modal = document.getElementById("comparativa-manual-overlay");
  if (modal) {
    document.body.removeChild(modal);
  }
  setTimeout(() => {
    mostrarConfirmacionAyuda();
  }, 500);
}

async function enviarComparativaManual(candidatoId) {
  const textoManual = document
    .getElementById("descripcion-vacante-manual")
    .value.trim();

  if (!textoManual) {
    Swal.fire(
      "⚠️",
      "Por favor ingresa la descripción de la vacante.",
      "warning"
    );
    return;
  }

  // Cerrar modal
  cerrarComparativaManual();

  // Mostrar mensaje en el chat
  addMessage(
    `Quiero comparar manualmente el CV con una descripción de vacante`,
    "user-message"
  );

  const loadingHTML = `
    <div class="candidate-analysis">
      <h5>🔍 Comparativa Manual en Proceso</h5>
      <div class="analysis-field">
        <strong>🔄 Proceso:</strong> Analizando compatibilidad del CV con la descripción manual...
      </div>
      <div class="analysis-field">
        <strong>📊 Estado:</strong> Consultando con IA...
      </div>
    </div>
  `;
  addHTMLMessage(loadingHTML, "bot-message");

  // Mostrar indicador de typing inicial
  const typingIndicator = mostrarTypingIndicator();

  setTimeout(async () => {
    ocultarTypingIndicator();

    // Mostrar mensaje de carga
    const loadingMessage = mostrarMensajeCarga(
      "Analizando compatibilidad del CV con la descripción manual..."
    );

    try {
      // Actualizar estado de carga
      if (loadingMessage) {
        loadingMessage.querySelector(".loading-text").textContent =
          "Extrayendo información del CV y procesando descripción manual...";
      }

      const response = await fetch(
        `${API_BASE}/comparar_manual/${candidatoId}/`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            texto_manual: textoManual,
          }),
        }
      );

      if (!response.ok) {
        throw new Error(`Error HTTP: ${response.status}`);
      }

      // Actualizar estado de carga
      if (loadingMessage) {
        loadingMessage.querySelector(".loading-text").textContent =
          "Calculando compatibilidad y generando análisis detallado...";
      }

      const data = await response.json();

      if (data.error) {
        throw new Error(data.error);
      }

      // Ocultar mensaje de carga
      ocultarMensajeCarga();

      // Mostrar resultados de la comparación
      mostrarResultadoComparativaManual(data, candidatoId);
    } catch (error) {
      console.error("Error en comparativa manual:", error);

      // Ocultar mensaje de carga
      ocultarMensajeCarga();

      const errorHTML = `
        <div class="chat-message bot-message">
          <div style="color: #dc3545; font-weight: bold;">❌ Error en comparativa manual</div>
          <p>No se pudo completar la comparación. Por favor intenta nuevamente.</p>
          <button class="special-btn" onclick="mostrarComparativaManual(${candidatoId})" 
                  style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%); margin-top: 10px;">
            🔄 Reintentar
          </button>
        </div>
      `;
      addHTMLMessage(errorHTML, "bot-message");
    }
  }, 1500); // Pequeña pausa para el efecto typing
}

// Función para mostrar resultados de la comparativa manual
function mostrarResultadoComparativaManual(data, candidatoId) {
  const candidato = candidatosData.find((c) => c.id_candidate == candidatoId);
  const score = data.score || 0;
  const scoreColor =
    score >= 80 ? "#28a745" : score >= 60 ? "#ffc107" : "#dc3545";
  const scoreText =
    score >= 80
      ? "Alta compatibilidad"
      : score >= 60
        ? "Compatibilidad media"
        : "Baja compatibilidad";

  const resultadoHTML = `
        <div class="candidate-analysis" style="border-left: 4px solid ${scoreColor};">
            <h5>📊 Resultado de Comparativa Manual</h5>
            
            <div class="analysis-field">
                <strong>👤 Candidato:</strong> ${candidato.nombre_candidate} ${candidato.apellidop_candidate}
            </div>
            
            <div class="analysis-field">
                <strong>🎯 Puntuación de Compatibilidad:</strong>
                <div style="background: ${scoreColor}; color: white; padding: 12px; border-radius: 8px; text-align: center; margin-top: 5px; font-weight: bold; font-size: 20px;">
                    ${score}/100 - ${scoreText}
                </div>
            </div>
            
            <div class="analysis-field">
                <strong>📋 Tipo:</strong> Comparación Manual vs Descripción Personalizada
            </div>
        </div>
        
        <div class="special-buttons" style="margin-top: 15px;">
            <button class="special-btn" onclick="mostrarComparativaManual(${candidatoId})" 
                    style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);">
                🔄 Nueva Comparación Manual
            </button>
            <button class="special-btn" onclick="activarComparacionCV()" 
                    style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                📊 Comparar con Puesto SAP
            </button>
        </div>
    `;

  addHTMLMessage(resultadoHTML, "bot-message");

  // Mostrar confirmación después de mostrar resultados
  setTimeout(() => {
    mostrarConfirmacionAyuda();
  }, 1000);
}

// DEMAS FUNCIONES //

// Función para seleccionar candidato desde la tabla
/*
function seleccionarParaAnalisis(id) {
  const candidato = candidatosData.find((c) => c.id_candidate == id);
  if (candidato) {
    // Ocultar todos los botones de selección
    toggleSelectionButtons(false);

    // Mostrar mensaje en el chat
    addMessage(
      `Seleccioné a ${candidato.nombre_candidate} ${candidato.apellidop_candidate} para análisis`,
      "user-message"
    );

    // Iniciar función de análisis
    iniciarAnalisisCandidato(candidato);
  }
}*/

// ============================================
// SISTEMA DE ANIMACIONES DE CARGA
// ============================================

// Mostrar indicador de "typing" del bot
function mostrarTypingIndicator() {
  const chatBody = document.getElementById("chat-body");
  if (!chatBody) return null;

  // Eliminar cualquier indicador existente
  const existingIndicator = document.getElementById("typing-indicator");
  if (existingIndicator) {
    existingIndicator.remove();
  }

  // Crear nuevo indicador
  const typingDiv = document.createElement("div");
  typingDiv.id = "typing-indicator";
  typingDiv.className = "typing-indicator";
  typingDiv.innerHTML = `
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
        <div class="typing-dot"></div>
    `;

  // Insertar antes de los contenedores especiales
  const opcionesMejora = document.getElementById("opciones-mejora-container");
  if (opcionesMejora) {
    chatBody.insertBefore(typingDiv, opcionesMejora);
  } else {
    chatBody.appendChild(typingDiv);
  }

  chatBody.scrollTop = chatBody.scrollHeight;
  return typingDiv;
}

// Ocultar indicador de typing
function ocultarTypingIndicator() {
  const indicator = document.getElementById("typing-indicator");
  if (indicator) {
    indicator.remove();
  }
}

// Mostrar mensaje de carga con animación
function mostrarMensajeCarga(mensaje = "Procesando con IA...") {
  const chatBody = document.getElementById("chat-body");
  if (!chatBody) return null;

  const loadingDiv = document.createElement("div");
  loadingDiv.id = "loading-message";
  loadingDiv.className = "loading-message";
  loadingDiv.innerHTML = `
        <div class="loading-text">${mensaje}</div>
        <div class="loading-animation">
            <div class="loading-dot"></div>
            <div class="loading-dot"></div>
            <div class="loading-dot"></div>
            <div class="loading-dot"></div>
        </div>
    `;

  // Insertar antes de los contenedores especiales
  const opcionesMejora = document.getElementById("opciones-mejora-container");
  if (opcionesMejora) {
    chatBody.insertBefore(loadingDiv, opcionesMejora);
  } else {
    chatBody.appendChild(loadingDiv);
  }

  chatBody.scrollTop = chatBody.scrollHeight;
  return loadingDiv;
}

// Ocultar mensaje de carga
function ocultarMensajeCarga() {
  const loadingMsg = document.getElementById("loading-message");
  if (loadingMsg) {
    loadingMsg.remove();
  }
}

// Mostrar botón deshabilitado con spinner
function mostrarBotonCargando(botonId, texto = "Procesando...") {
  const boton = document.getElementById(botonId);
  if (!boton) return;

  // Guardar estado original
  boton.dataset.originalText = boton.innerHTML;
  boton.dataset.originalDisabled = boton.disabled;

  // Actualizar botón
  boton.innerHTML = `
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        ${texto}
    `;
  boton.disabled = true;
}

// Restaurar botón a su estado original
function restaurarBoton(botonId) {
  const boton = document.getElementById(botonId);
  if (!boton || !boton.dataset.originalText) return;

  boton.innerHTML = boton.dataset.originalText;
  boton.disabled = boton.dataset.originalDisabled === "true";

  // Limpiar datos
  delete boton.dataset.originalText;
  delete boton.dataset.originalDisabled;
}
