const urlConfig = `http://localhost/Chatbot-AdminCenter/modelo/getConfig.php`;
let windowConfig = {};
let chatbotMinimizado = false;
let flujoConversacion = "";
let estadoConversacion = "";
let temaEnCurso = false;
let csvDataPorColumna = {};

//FUNCIONES DE ESTILOS
function aplicarEstilosBotones(contenedor) {
  if (!windowConfig) return;
  const botones = contenedor.querySelectorAll("button");
  botones.forEach(boton => {
    // Estilo base
    boton.style.backgroundColor = windowConfig.colorSecundario;
    boton.style.color = windowConfig.colorTexto;
    boton.style.borderColor = windowConfig.colorAcento || windowConfig.colorPrimario;
    boton.style.transition = "background-color 0.3s ease, transform 0.1s ease";

    // Hover
    boton.addEventListener("mouseenter", () => {
      boton.style.backgroundColor = windowConfig.colorAcento;
    });
    boton.addEventListener("mouseleave", () => {
      boton.style.backgroundColor = windowConfig.colorSecundario;
    });

    // Click (efecto visual)
    boton.addEventListener("mousedown", () => {
      boton.style.backgroundColor = windowConfig.colorAcento;
      boton.style.transform = "scale(0.95)";
    });
    boton.addEventListener("mouseup", () => {
      boton.style.backgroundColor = windowConfig.colorSecundario;
      boton.style.transform = "scale(1)";
    });
  });
}

//-------------CARGAR CONFIGURACIÓN------------------
async function cargarConfigChatbot() {
  try {
    const response = await fetch(urlConfig);
    if (!response.ok) throw new Error("No se pudo cargar la configuración del chatbot");

    const config = await response.json();
    windowConfig = config;

    // CONFIGURACIÓN VISUAL Y DINÁMICA 
    const chatbotContainer = document.getElementById("chatbot-container");
    if (chatbotContainer) {
      const header = chatbotContainer.querySelector(".chatbot-header");
      if (header) {
        header.style.backgroundColor = config.colorPrimario;
        header.style.color = config.colorTexto;

        const logo = header.querySelector(".chatbot-icon");
        if (logo) logo.src = config.urlLogotipo;

        const nombreElemento = header.querySelector(".chatbot-nombre");
        if (nombreElemento) nombreElemento.textContent = config.inp_nombre;

        if (config.colorTexto) {
          aplicarColorBotonesSVG(config.colorTexto);
        }
      }

      const mensajeInicial = document.getElementById("mensaje-inicial");
      if (mensajeInicial) {
        mensajeInicial.querySelector("p").textContent = config.inp_saludo;

        const botonesContainer = mensajeInicial.querySelector(".chatbot-button-container");
        botonesContainer.innerHTML = "";

         const temas = [
  { 
    tema: config.inp_conversa1,
    mensaje: config.inp_mensaje_usuario,
    columna: config.inp_columna,
    urlInforme: config.inp_url_informe
  },
  { 
    tema: config.inp_conversa2,
    mensaje: config.inp_mensaje_usuario2,
    columna: config.inp_columna2,
    urlInforme: config.inp_url_informe
  },
  { 
    tema: config.inp_conversa3,
    mensaje: config.inp_mensaje_usuario3,
    columna: config.inp_columna3,
    urlInforme: config.inp_url_informe3
  }
].filter(t => t.tema);

windowConfig.conversacion = temas;


        temas.forEach(conver => {
          const btn = document.createElement("button");
          btn.textContent = conver.tema;
          btn.onclick = () => manejarTema(conver);
          botonesContainer.appendChild(btn);
        });
        aplicarEstilosBotones(mensajeInicial);
      }

      const chatText = document.getElementById("chatText");
      if (chatText) {
        chatText.textContent = config.inp_burbuja || "";
        chatText.style.backgroundColor = config.colorPrimario;
        chatText.style.color = config.colorTexto;
      }

      const chatToggle = document.querySelector(".toggle-icon");
      if (chatToggle) chatToggle.src = config.urlLogotipo;
    }

  } catch (error) {
    console.error("Error al cargar la configuración:", error);
    agregarMensajeChatbot("Ocurrió un error al cargar la configuración del chatbot.");
  }
}

function aplicarColorBotonesSVG(color) {
  const iconos = document.querySelectorAll(".chatbot-min svg, .chatbot-close svg");
  iconos.forEach(svg => {
    svg.style.color = color; // currentColor se aplicará automáticamente a fill o stroke
  });
}


// -------------------- MANEJAR TEMAS --------------------
function manejarTema(conver) {
  if (temaEnCurso) return;
  if (!conver) return;

  temaEnCurso = true;
  agregarMensajeChatbot(conver.mensaje || "Selecciona una opción:");

  if (conver.urlInforme && conver.columna) {
    const esSeguimiento = conver.tema.toLowerCase().includes("seguimiento");
    if (esSeguimiento) {
      seguimientoPostulacion(); // Mostrar input
    } else {
      cargarCSV(conver.urlInforme, conver.columna, false);
    }
  }
}


//  CARGAR CSV 
function cargarCSV(url, columnaClave, esSeguimiento = false) {
  Papa.parse(url, {
    download: true,
    header: true,
    skipEmptyLines: true,
    complete: function (results) {
      // Guardar los datos por columna
      csvDataPorColumna[columnaClave] = results.data;

      // Si no es seguimiento, mostrar select
      if (!esSeguimiento) {
        // Limpiar select anterior de la misma columna
        const selectExistente = document.getElementById(`seleccion-${columnaClave}`);
        if (selectExistente) selectExistente.remove();

        mostrarSelect(
          columnaClave,
          `Buscando vacantes en`,
          `seleccion-${columnaClave}`,
          `Seleccione una opción`
        );
      }
    },
    error: function (err) {
      agregarMensajeChatbot("No se pudo cargar la lista de opciones.");
      console.error(err);
    }
  });
}
//--------------------------FUNCIÓN PARA MOSTRAR EL SELECT DE LAS OPCIONES DE VACANTES------------------
function mostrarSelect(columnaClave, mensajeUsuario, selectId, textoDefault) {
  const contenedor = document.querySelector(".chatbot-body");
  

  const selectExistente = document.getElementById(selectId);
  if (selectExistente) selectExistente.remove();

  const select = document.createElement("select");
  select.id = selectId;
  select.style.marginTop = "10px";

  

  select.onchange = function () {
    const valorSeleccionado = this.value;

    const div = document.createElement("div");
    div.className = "user-message2";
    div.innerHTML = `${mensajeUsuario} ${valorSeleccionado}...`;

    if (windowConfig) {
      div.style.backgroundColor = windowConfig.colorRespuestaUsuario;
      div.style.color = windowConfig.colorTexto;
      div.style.borderRadius = "12px";
      div.style.padding = "8px 12px";
      div.style.maxWidth = "80%";
      div.style.margin = "5px 0";
    }

    contenedor.appendChild(div);
    div.scrollIntoView({ behavior: "smooth" });

    select.disabled = true;

    // Usar CSV correspondiente a esta columna
    const csvData = csvDataPorColumna[columnaClave] || [];
    const resultados = csvData.filter(item => item[columnaClave] === valorSeleccionado);

    if (resultados.length === 0) {
      agregarMensajeChatbot(`No se encontraron resultados en ${valorSeleccionado}`);
    } else {
       resultados.forEach(emp => {
        let mensaje = "<div class='resultado-csv'>";
        let link = null; 

        for (const key in emp) {
          if (emp.hasOwnProperty(key) && emp[key]) {
            if (key.toLowerCase() === "link") {
              link = emp[key]; // Guardar el link para mostrar al final
            } else {
              mensaje += `<p><strong>${key}:</strong> ${emp[key]}</p>`;
            }
          }
        }

        // Mostrar el link al final si existe
        if (link) {
         if (windowConfig.sftp_activo) {
            mensaje += `
            <button class="btn btn-primary" onclick="abrirModalPostulacion('${emp['ID de requisición de personal']}')">
                Postúlate
            </button>
        `;
          } else {
            mensaje += `<p><a href="${link}" target="_blank">Postúlate</a></p>`;
          }
        }
        mensaje += "</div>";
        agregarMensajeChatbot(mensaje);
      });

    }
    setTimeout(confirmacionAyuda, 1000);
  };

  const csvData = csvDataPorColumna[columnaClave] || [];
  const opcionesUnicas = [...new Set(csvData.map(row => row[columnaClave]).filter(Boolean))];

  const defaultOption = document.createElement("option");
  defaultOption.text = textoDefault;
  defaultOption.disabled = true;
  defaultOption.selected = true;
  select.appendChild(defaultOption);

  opcionesUnicas.forEach(opt => {
    const option = document.createElement("option");
    option.value = opt;
    option.text = opt;
    select.appendChild(option);
  });

  contenedor.appendChild(select);
  select.scrollIntoView({ behavior: "smooth" });
  aplicarEstilosBotones(contenedor);
}

// -------------------- MOSTRAR EMPLEOS --------------------
function mostrarEmpleos(categoria, columnaClave) {
  const empleos = csvData.filter(row => row[columnaClave] === categoria);
  if (empleos.length === 0) {
    agregarMensajeChatbot(`No hay vacantes en la categoría ${categoria}`);
    return;
  }

  agregarMensajeChatbot(`Vacantes encontradas en ${categoria}:`);

  empleos.forEach(emp => {
    const contenedor = document.querySelector(".chatbot-body");
    const div = document.createElement("div");
    div.classList.add("chatbot-message");

    const link = document.createElement("a");
    link.href = emp.link; // columna "link" en tu CSV
    link.textContent = emp.titulo || "Ver empleo";
    link.target = "_blank";

    div.appendChild(link);
    contenedor.appendChild(div);
  });

  confirmacionAyuda();
}
// FUNCIONES DEL CHATBOT
function toggleChatbot() {
  const chatbotContainer = document.getElementById("chatbot-container");
  const chatToggle = document.getElementById("chatbot-toggle");
  const chatText = document.getElementById("chatText");

  chatbotMinimizado = !chatbotMinimizado;

  if (chatbotContainer.classList.contains("open")) {
    chatbotContainer.classList.remove("open");
    chatToggle.classList.add("burbuja-parpadeante");
    setTimeout(() => chatText.classList.remove("hidden"), 500);
  } else {
    chatToggle.classList.remove("burbuja-parpadeante");
    chatText.classList.add("hidden");
    chatbotContainer.style.display = "block";
    setTimeout(() => chatbotContainer.classList.add("open"), 10);
  }
}

function cerrar() {
  const contenidoInicial = `
          <div id="mensaje-inicial" class="chatbot-message">
              <p>${windowConfig.inp_saludo || "¡Hola! Soy tu asistente virtual!"}</p>
              <div class="chatbot-button-container">
                  <button onclick="mostrarPreguntaPerfil()">Buscar vacantes por categoría</button>
                  <button onclick="iniciarBusquedaPorUbicacion()">Buscar vacantes por ubicación</button>
                  <button onclick="seguimientoPostulacion()">Seguimiento de mi postulación</button>
              </div>
          </div>`;
  const chatbotBody = document.querySelector(".chatbot-body");
  chatbotBody.innerHTML = contenidoInicial;
  aplicarEstilosBotones(chatbotBody);
  toggleChatbot();
}

// -------------------- INICIALIZACIÓN --------------------
document.addEventListener("DOMContentLoaded", async () => {
 await cargarConfigChatbot();

  const chatToggle = document.getElementById("chatbot-toggle");
  chatToggle.classList.add("burbuja-parpadeante");
  chatToggle.addEventListener("click", toggleChatbot);

  const inputPerfil = document.getElementById("user-input");
  if (inputPerfil) {
    inputPerfil.addEventListener("keypress", function (event) {
      if (event.key === "Enter") {
        event.preventDefault();
        enviarRespuesta();
      }
    });
  }
});

// -------------------- FUNCIONES DE MENSAJES --------------------
function agregarMensajeChatbot(texto) {
  const contenedor = document.querySelector(".chatbot-body");
  const div = document.createElement("div");
  div.className = "chatbot-message";
  div.innerHTML = texto;
  contenedor.appendChild(div);
  div.scrollIntoView({ behavior: "smooth" });
  aplicarEstilosBotones(div);
}

function agregarMensajeUsuario(texto) {
  if (texto.trim() === "") return;
  const contenedor = document.querySelector(".chatbot-body");
  const div = document.createElement("div");
  div.className = "user-message2";
  div.innerHTML = `<p>${texto}</p>`;
  contenedor.appendChild(div);

  if (windowConfig) {
    div.style.backgroundColor = windowConfig.colorRespuestaUsuario;
    div.style.color = windowConfig.colorTexto;
  }

  div.scrollIntoView({ behavior: "smooth" });
}
///////////////////////////////////////////////////////////////////////////////////
function mostrarPreguntaPerfil() {
  if (!windowConfig.conversacion || windowConfig.conversacion.length === 0) {
    agregarMensajeChatbot("No hay temas configurados para vacantes por categoría.");
    return;
  }

  const conver = windowConfig.conversacion.find(
    c => c.urlInforme && c.columna.toLowerCase() !== "puesto"
  );

  if (!conver) {
    agregarMensajeChatbot("No se encontró información disponible para esta opción.");
    return;
  }

  agregarMensajeChatbot(conver.mensaje);
  cargarCSV(conver.urlInforme, conver.columna);
}


function iniciarBusquedaPorUbicacion() {
  if (!windowConfig.conversacion || windowConfig.conversacion.length === 0) {
    agregarMensajeChatbot("No hay temas configurados para vacantes por ubicación.");
    return;
  }

  const conver = windowConfig.conversacion.find(
    c => c.urlInforme && c.columna.toLowerCase() !== "puesto" && c.tema.toLowerCase().includes("ubicación")
  );

  if (!conver) {
    agregarMensajeChatbot("No hay información disponible para ubicaciones.");
    return;
  }

  agregarMensajeChatbot(conver.mensaje);
  cargarCSV(conver.urlInforme, conver.columna, false);
}


//Modal del postulante
//Modal del postulante (modo demostrativo)
function abrirModalPostulacion(idRequisicion) {
  const existente = document.getElementById("modal-postulacion");
  if (existente) existente.remove();

  const modalHTML = `
    <div class="modal fade" id="modal-postulacion" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title">Completa tu postulación</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <form id="formPostulacion" enctype="multipart/form-data">
            <div class="modal-body">
              <!-- Paso 1: Ingresar correo -->
              <div id="paso1">
                <div class="form-group mb-3">
                  <label>Correo electrónico</label>
                  <input type="email" id="correo_candidate" name="correo_candidate"
                         class="form-control" style="border-radius:25px;">
                </div>
                <button type="button" class="btn btn-primary" id="btnVerificarCorreo">Siguiente</button>
              </div>

              <!-- Paso 2: Datos completos -->
              <div id="paso2" style="display:none;">
                <div class="form-group mb-3">
                  <label>Nombre</label>
                  <input type="text" id="nombre_candidate" class="form-control" style="border-radius:25px;">
                </div>
                <div class="form-group mb-3">
                  <label>Apellido Paterno</label>
                  <input type="text" id="apellidop_candidate" class="form-control" style="border-radius:25px;">
                </div>
                <div class="form-group mb-3">
                  <label>Apellido Materno</label>
                  <input type="text" id="apellidom_candidate" class="form-control" style="border-radius:25px;">
                </div>
                <div class="form-group mb-3">
                  <label>Teléfono</label>
                  <input type="text" id="tel_candidate" class="form-control" style="border-radius:25px;">
                </div>
                <div class="form-group mb-3">
                  <label>Currículum (PDF)</label>
                  <input type="file" id="CV_candidate" class="form-control" style="border-radius:25px;">
                </div>
                <div id="cvInfo"></div>
              </div>

            </div>
            <div class="modal-footer">
              <button type="submit" id="btnEnviar" class="btn btn-success" style="display:none;">Enviar</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            </div>
          </form>

        </div>
      </div>
    </div>`;

  document.body.insertAdjacentHTML('beforeend', modalHTML);
  const modal = document.getElementById("modal-postulacion");

  // Blur visual al fondo
  const bodyChildren = Array.from(document.body.children).filter(c => c !== modal);
  bodyChildren.forEach(el => el.classList.add('blur-background'));

  $(modal).modal('show');

  $(modal).on('hidden.bs.modal', function () {
    bodyChildren.forEach(el => el.classList.remove('blur-background'));
    modal.remove();
  });

  aplicarEstilosModal();

  // -------- SIMULACIÓN DE VERIFICAR CORREO --------
  $('#btnVerificarCorreo').on('click', function () {
    const correo = $('#correo_candidate').val().trim();
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!regex.test(correo)) {
      alert("Ingresa un correo válido");
      return;
    }

    // Simular carga y mostrar siguiente paso
    Swal.fire({
      icon: 'info',
      title: 'Modo demostración',
      text: 'Simulación: verificación de correo exitosa',
      confirmButtonColor: windowConfig.colorPrimario || '#4caf50'
    }).then(() => {
      $('#paso1').hide();
      $('#paso2').show();
      $('#btnEnviar').show();
    });
  });

  // -------- SIMULACIÓN DE ENVÍO FINAL --------
  $('#formPostulacion').on('submit', function (e) {
    e.preventDefault();

    Swal.fire({
      icon: 'success',
      title: 'Postulación simulada',
      text: 'Esta acción no se almacena porque estás en el modo interactivo del Centro de Administración.',
      confirmButtonColor: windowConfig.colorPrimario || '#4caf50'
    }).then(() => {
      $('#modal-postulacion').modal('hide');
    });
  });
}


// -------------------- SEGUIMIENTO DE POSTULACIONES --------------------
function seguimientoPostulacion() {
  flujoConversacion = "seguimiento";
  window.temaSeguimiento = windowConfig.conversacion.find(c => c.tema.toLowerCase().includes("seguimiento"));

  if (!window.temaSeguimiento) {
    agregarMensajeChatbot("No hay tema de seguimiento configurado.");
    return;
  }

  estadoConversacion = "preguntaUsuario";

  if (window.temaSeguimiento.urlInforme && window.temaSeguimiento.columna) {
    cargarCSV(window.temaSeguimiento.urlInforme, window.temaSeguimiento.columna, true);
  }

  // Mostrar input
  const inputContainer = document.getElementById("user-input-container");
  if (inputContainer) inputContainer.style.display = "flex";

  const userInput = document.getElementById("user-input");
  if (userInput) {
    userInput.disabled = false;
    userInput.value = "";
    userInput.focus();
  }

  const btnEnviar = inputContainer.querySelector("button");
  if (btnEnviar && windowConfig.colorPrimario) {
    btnEnviar.style.backgroundColor = windowConfig.colorPrimario;
    btnEnviar.style.color = windowConfig.colorTexto || "#fff";
    btnEnviar.style.borderColor = windowConfig.colorAcento || windowConfig.colorPrimario;
    btnEnviar.style.padding = "8px 15px";
    btnEnviar.style.borderRadius = "15px";
    btnEnviar.style.cursor = "pointer";
  }
}
//---------------FUNCIÓN DE MANEJO DEL FLUJO DEL SEGUIMIENTO-------------
function manejarFlujoSeguimiento(userInput) {
  if (estadoConversacion !== "preguntaUsuario") return;

  const columna = window.temaSeguimiento.columna;
  const csvData = csvDataPorColumna[columna] || [];
  const resultados = csvData.filter(item => item[columna] === userInput);

  if (resultados.length > 0) {
    agregarMensajeChatbot("Postulaciones encontradas:");

    resultados.forEach(item => {
      let html = "<div class='resultado-csv'>";
      Object.keys(item).forEach(col => {
        if (item[col]) html += `<p><strong>${col}:</strong> ${item[col]}</p>`;
      });
      html += "</div>";
      agregarMensajeChatbot(html);
    });
  } else {
    agregarMensajeChatbot("No se encontraron coincidencias con tu información.");
  }

  // Ocultar input
  const inputContainer = document.getElementById("user-input-container");
  if (inputContainer) inputContainer.style.display = "none";

  confirmacionAyuda();
  estadoConversacion = "finalizado";
}
//--------------------ESTILOS DEL JSON PARA EL MODAL
function aplicarEstilosModal() {
  if (!windowConfig) return;

  const btnSiguiente = document.getElementById("btnVerificarCorreo");
  const btnEnviar = document.getElementById("btnEnviar");
  const btnCancelar = document.querySelector("#modal-postulacion .btn-secondary");

  [btnSiguiente, btnEnviar].forEach(btn => {
    if (btn) {
      btn.style.backgroundColor = windowConfig.colorPrimario;
      btn.style.color = windowConfig.colorTexto || "#fff";
      btn.style.border = "none";
      btn.style.padding = "8px 15px";
      btn.style.borderRadius = "15px";
      btn.style.cursor = "pointer";
      btn.style.transition = "background-color 0.3s ease, transform 0.1s ease";

      // Hover
      btn.addEventListener("mouseenter", () => {
        btn.style.backgroundColor = windowConfig.colorAcento || windowConfig.colorPrimario;
      });
      btn.addEventListener("mouseleave", () => {
        btn.style.backgroundColor = windowConfig.colorPrimario;
      });

      // Click efecto
      btn.addEventListener("mousedown", () => {
        btn.style.transform = "scale(0.95)";
      });
      btn.addEventListener("mouseup", () => {
        btn.style.transform = "scale(1)";
      });
    }
  });

  // Opcional: estilo para Cancelar
  if (btnCancelar) {
    btnCancelar.style.borderRadius = "15px";
    btnCancelar.style.padding = "8px 15px";
  }
}


// -------------------- ENVIAR RESPUESTA --------------------
function enviarRespuesta() {
  const userInputField = document.getElementById("user-input");
  const userInput = userInputField.value.trim();
  if (userInput === "") return;

  agregarMensajeUsuario(userInput);

  if (flujoConversacion === "seguimiento") {
    manejarFlujoSeguimiento(userInput);
  }

  userInputField.value = "";
}


// -------------------- MANEJO DEL FLUJO DE SEGUIMIENTO --------------------
function manejarFlujoSeguimiento(userInput) {
  if (estadoConversacion !== "preguntaUsuario") return;

  const columnaClave = window.temaSeguimiento.columna;
  const csvData = csvDataPorColumna[columnaClave] || [];

  const resultados = csvData.filter(item => {
    const valor = item[columnaClave] || "";
    return valor.toString().trim().toLowerCase() === userInput.toLowerCase().trim();
  });

  if (resultados.length > 0) {
    agregarMensajeChatbot("Postulaciones encontradas:");

    resultados.forEach(item => {
      let html = "<div class='resultado-csv'>";
      
      // Mostrar solo columnas "reales"
      Object.keys(item).forEach(col => {
        if (col && !col.startsWith("_")) { // Ignorar columnas automáticas de PapaParse
          html += `<p><strong>${col}:</strong> ${item[col] || '-'}</p>`;
        }
      });

      html += "</div>";
      agregarMensajeChatbot(html);
    });
  } else {
    agregarMensajeChatbot("No se encontraron coincidencias con tu información.");
  }

  // Ocultar input
  const inputContainer = document.getElementById("user-input-container");
  if (inputContainer) inputContainer.style.display = "none";

  confirmacionAyuda();
  estadoConversacion = "finalizado";
}

// -------------------- VALIDAR EMAIL --------------------
function validateEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}


// CONFIRMACIÓN FINAL
function confirmacionAyuda() {
  const htmlBotones = `
          <div class="chatbot-message-buttons" style="margin-top: 20px;">
              <button class="btnSi" style="margin-right: 5px; border-radius: 15px; padding: 4px 12px;">Sí</button>
              <button class="btnNo" style="border-radius: 15px; padding: 4px 10px;"  >No</button>
          </div>`;
  agregarMensajeChatbot("¿Puedo ayudarte con algo más? " + htmlBotones);
  //const inputContainer = document.getElementById("user-input-container");
  //if (inputContainer) inputContainer.style.display = "none";

  setTimeout(() => {
    const contenedor = document.querySelector(".chatbot-body");
    const botones = contenedor.querySelectorAll(".chatbot-message-buttons");
    const ultimo = botones[botones.length - 1];

    ultimo.querySelector(".btnSi").addEventListener("click", () => {
      funcionSi();
      bloquearBotones(ultimo);
    });
    ultimo.querySelector(".btnNo").addEventListener("click", () => {
      funcionNo();
      bloquearBotones(ultimo);
    });
  }, 0);
}

function bloquearBotones(ultimo) {
  ultimo.querySelector(".btnSi").disabled = true;
  ultimo.querySelector(".btnNo").disabled = true;
}

function funcionSi() {
  temaEnCurso = false;
  const contenidoInicial = `
          <div id="mensaje-inicial" class="chatbot-message">
              <p>¡Con gusto! ¿En qué más puedo ayudarte?</p>
              <div class="chatbot-button-container">
                  <button onclick="mostrarPreguntaPerfil()">Buscar vacantes por categoría</button>
                  <button onclick="iniciarBusquedaPorUbicacion()">Buscar vacantes por ubicación</button>
                  <button onclick="seguimientoPostulacion()">Seguimiento de mi postulación</button>
              </div>
          </div>`;
  const contenedor = document.querySelector(".chatbot-body");
  contenedor.insertAdjacentHTML("beforeend", contenidoInicial);
  contenedor.lastElementChild.scrollIntoView({ behavior: "smooth" });
  aplicarEstilosBotones(contenedor.lastElementChild);
}

function funcionNo() {
  setTimeout(() => {
    temaEnCurso = false;
    agregarMensajeChatbot(`<div style="text-align:center;"><p>${windowConfig.despedida || "¡Gracias por usar nuestro asistente virtual!"}</p></div>`);
  }, 500);
  setTimeout(cerrar, 3500);
}
