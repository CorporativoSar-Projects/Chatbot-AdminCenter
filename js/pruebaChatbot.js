const urlConfig = `http://localhost/Chatbot-AdminCenter/modelo/getConfig.php`;
// const urlConfig = `http://localhost/Chatbot-AdminCenter-JessicaMoralesAguilar/modelo/getConfig.php`;
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

        console.log(config);
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
            urlInforme: config.inp_url_informe // opcional si agregas url_informe2
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
  // Deshabilitar todos los botones mientras se procesa este tema
  const botones = document.querySelectorAll("#mensaje-inicial button");
  botones.forEach(btn => btn.disabled = true);

  if (!conver) return;

  temaEnCurso = true;
  agregarMensajeChatbot(conver.mensaje || "Selecciona una opción:");

  const esSeguimiento = conver.tema.toLowerCase().includes("seguimiento");

  if (esSeguimiento) {
    // Activa el flujo correcto de seguimiento
    flujoConversacion = "seguimiento";
    activarCajaSeguimiento();
    seguimientoPostulacion();
  } else {
    flujoConversacion = "ia";
    activarCajaIA();
    if (conver.urlInforme && conver.columna) {
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


  // Mapa de columnas internas → nombres amigables
  const nombresColumnas = {
    //"reqId_ix": "ID de vacante",
    "title_ix": "Nombre de la vacante",
    "category_ix": "Categoría",
    "location_ix": "Ubicación",
    // agrega más columnas aquí según tu CSV
  };

  select.onchange = function () {
    const valorSeleccionado = this.value;

    const div = document.createElement("div");
    div.className = "user-message2";
    div.innerHTML = `${mensajeUsuario} ${valorSeleccionado}...`;


    //if (windowConfig) {
    //div.style.backgroundColor = windowConfig.colorRespuestaUsuario;
    //div.style.color = windowConfig.colorTexto;
    //div.style.borderRadius = "12px";
    //div.style.padding = "8px 12px";
    //div.style.maxWidth = "80%";
    //div.style.margin = "5px 0";
    //}


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
          const tipoIntegracion = windowConfig.tipo_integracion || "estandar";
          const integracionActiva = windowConfig.integracion_activa === true;
          const baseUrl = windowConfig.url_estandar || "";
          if (integracionActiva) {

            //  INTEGRACIÓN SFTP
            if (tipoIntegracion === "sftp") {

              mensaje += `
              <button class="btn btn-primary"
                  onclick="abrirModalPostulacion('${emp['reqId_ix']}', '${emp['title_ix']}')">
                  Postúlate
              </button>
          `;
            }

            // INTEGRACIÓN ESTÁNDAR (URL)
            else if (tipoIntegracion === "estandar") {

              const reqId = emp["reqId_ix"] || "";

              // Si hay URL de carreras definida
              const urlFinal = (baseUrl && reqId)
                ? `${baseUrl}${reqId}`
                : link;

              mensaje += `
              <p><a href="${urlFinal}" target="_blank">
                  Postúlate
              </a></p>
          `;
            }

          } else {
            // Sin integración activa → usa el link del CSV
            mensaje += `
          <p><a class="btn btn-primary" href="${link}" target="_blank">
              Postúlate
          </a></p>
      `;
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

function cerrar(){
  activarCajaIA();
  const chatbotBody = document.querySelector(".chatbot-body");
  chatbotBody.innerHTML = "";

  const mensajeInicial = document.createElement("div");
  mensajeInicial.id = "mensaje-inicial";
  mensajeInicial.className = "chatbot-message";

  const saludo = document.createElement("p");
  saludo.textContent = windowConfig.inp_saludo || "¡Hola! Soy tu asistente virtual!";
  mensajeInicial.appendChild(saludo);

  const botonesContainer = document.createElement("div");
  botonesContainer.className = "chatbot-button-container";

  if (windowConfig.conversacion) {
    windowConfig.conversacion.forEach(conver => {
      const btn = document.createElement("button");
      btn.textContent = conver.tema;
      btn.onclick = () => manejarTema(conver);
      botonesContainer.appendChild(btn);
    });
  }

  mensajeInicial.appendChild(botonesContainer);
  chatbotBody.appendChild(mensajeInicial);
  aplicarEstilosBotones(mensajeInicial);

  // Restaurar inputs
  const aiInputContainer = document.getElementById("ai-chat-container");
  const userInputContainer = document.getElementById("user-input-container");

  if (aiInputContainer) aiInputContainer.style.display = "flex";
  if (userInputContainer) userInputContainer.style.display = "none";

  const userInput = document.getElementById("user-input");
  if (userInput) userInput.value = "";

  temaEnCurso = false;
  chatbotMinimizado = false;
  toggleChatbot();
}

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
      Object.keys(item).forEach(col => {
        if (col && !col.startsWith("_")) {
          html += `<p><strong>${col}:</strong> ${item[col] || '-'}</p>`;
        }
      });
      html += "</div>";
      agregarMensajeChatbot(html);
    });
  } else {
    agregarMensajeChatbot("No se encontraron coincidencias con tu información.");
  }

  setTimeout(confirmacionAyuda, 1000);

  // Cambiar visibilidad de inputs
  const userInputContainer = document.getElementById("user-input-container");
  const aiInputContainer = document.getElementById("ai-chat-container");
  if (userInputContainer) userInputContainer.style.display = "none";
  if (aiInputContainer) aiInputContainer.style.display = "flex";

  estadoConversacion = "finalizado";
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
  activarCajaSeguimiento();
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
  const input = document.getElementById("user-input");
  const texto = input.value.trim();

  if (!texto) return; // Evita mensajes vacíos

  // Mostrar el mensaje del usuario
  agregarMensajeUsuario(texto);

  // Limpiar el campo de texto
  input.value = "";

  // Si el flujo activo es de seguimiento, manejar la búsqueda directamente
  if (flujoConversacion === "seguimiento") {
    manejarFlujoSeguimiento(texto);
    return;
  }

  // Si no hay flujo activo, mostrar un aviso local (sin API)
  agregarMensajeChatbot(" No hay sesión.");
}


// -------------------- MANEJO DEL FLUJO DE SEGUIMIENTO --------------------
function manejarFlujoSeguimiento(userInput) {
  if (estadoConversacion !== "preguntaUsuario") return;

    // Validar que el input sea un email
  if (!validateEmail(userInput)) {
    agregarMensajeChatbot("Por favor, ingresa un correo electrónico válido.");
    return; // No continuar hasta que sea válido
  }

  const columnaClave = window.temaSeguimiento.columna;
  const csvData = csvDataPorColumna[columnaClave] || [];

  const resultados = csvData.filter(item => {
    const valor = (item[columnaClave] || "").trim().toLowerCase();
    return valor === userInput.trim().toLowerCase();
  });

  if (resultados.length > 0) {
    agregarMensajeChatbot("🔎 Postulaciones encontradas:");
    resultados.forEach(item => {
      let html = "<div class='resultado-csv'>";
      Object.keys(item).forEach(col => {
        if (col && !col.startsWith("_")) {
          html += `<p><strong>${col}:</strong> ${item[col] || '-'}</p>`;
        }
      });
      html += "</div>";
      agregarMensajeChatbot(html);
    });
  } else {
    agregarMensajeChatbot("No se encontraron coincidencias con tu información.");
  }

  setTimeout(() => {confirmacionAyuda();}, 1000);

  // Volver a modo IA solo después de terminar
  flujoConversacion = "ia";
  estadoConversacion = "finalizado";

  activarCajaIA();
}

// -------------------- VALIDAR EMAIL --------------------
function validateEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}


// CONFIRMACIÓN FINAL
function confirmacionAyuda() {
  const contenedor = document.querySelector(".chatbot-body");

  // Crear contenedor de mensaje manualmente
  const divMensaje = document.createElement("div");
  divMensaje.className = "chatbot-message";
  divMensaje.innerHTML = "<p>¿Puedo ayudarte con algo más?</p>";

  // Crear contenedor de botones
  const botonesDiv = document.createElement("div");
  botonesDiv.className = "chatbot-message-buttons";
  botonesDiv.style.marginTop = "20px";

  // Botón "Sí"
  const btnSi = document.createElement("button");
  btnSi.textContent = "Sí";
  btnSi.className = "btnSi";
  btnSi.style.marginRight = "5px";
  btnSi.style.borderRadius = "15px";
  btnSi.style.padding = "4px 12px";

  // Botón "No"
  const btnNo = document.createElement("button");
  btnNo.textContent = "No";
  btnNo.className = "btnNo";
  btnNo.style.borderRadius = "15px";
  btnNo.style.padding = "4px 10px";

  // Agregar botones al div
  botonesDiv.appendChild(btnSi);
  botonesDiv.appendChild(btnNo);

  // Agregar botones al mensaje
  divMensaje.appendChild(botonesDiv);

  // Insertar mensaje en el chat
  contenedor.appendChild(divMensaje);
  divMensaje.scrollIntoView({ behavior: "smooth" });

  // Listeners de botones
  btnSi.addEventListener("click", () => {
    funcionSi();
    bloquearBotones(botonesDiv);
  });

  btnNo.addEventListener("click", () => {
    funcionNo();
    bloquearBotones(botonesDiv);
  });
}

function bloquearBotones(ultimo) {
  ultimo.querySelector(".btnSi").disabled = true;
  ultimo.querySelector(".btnNo").disabled = true;
}

function funcionSi() {
  temaEnCurso = false;
  mensajeSeguimientoMostrado = false;

  // Crear contenedor como elemento HTML
  const contenidoInicial = document.createElement("div");
  contenidoInicial.className = "chatbot-message";
  contenidoInicial.innerHTML = `
      <p>¡Con gusto! ¿En qué más puedo ayudarte?</p>
      <div class="chatbot-button-container">
          <button>Buscar vacantes por categoría</button>
          <button>Buscar vacantes por ubicación</button>
          <button>Seguimiento de mi postulación</button>
      </div>
  `;

  const contenedor = document.querySelector(".chatbot-body");
  contenedor.appendChild(contenidoInicial);
  contenidoInicial.scrollIntoView({ behavior: "smooth" });
  aplicarEstilosBotones(contenidoInicial);

  // Asignar click con bloqueo a cada botón
  const botones = contenidoInicial.querySelectorAll("button");
  botones.forEach(btn => {
    btn.addEventListener("click", () => {
      // Bloquear todos los botones
      botones.forEach(b => b.disabled = true);

      if (btn.textContent.includes("categoría")) {
        mostrarPreguntaPerfil();
      }
      else if (btn.textContent.includes("ubicación")) {
        iniciarBusquedaPorUbicacion();
      }
      else if (btn.textContent.includes("Seguimiento")) {
        // Buscar tema de seguimiento en el JSON
        const temaSeguimiento = windowConfig.conversacion.find(c => c.tema.toLowerCase().includes("seguimiento"));

        // Siempre mostrar el mensaje del JSON
        if (temaSeguimiento && temaSeguimiento.mensaje) {
          agregarMensajeChatbot(temaSeguimiento.mensaje);
        }



        // Ejecutar la acción según el texto del botón
        if (btn.textContent.includes("categoría")) mostrarPreguntaPerfil();
        else if (btn.textContent.includes("ubicación")) iniciarBusquedaPorUbicacion();
        else if (btn.textContent.includes("Seguimiento")) seguimientoPostulacion();
      }
    });
  });
}

function funcionNo() {
  temaEnCurso = false;

  const contenedor = document.querySelector(".chatbot-body");

  const mensajeDespedida = document.createElement("div");
  mensajeDespedida.className = "chatbot-message";
  mensajeDespedida.style.display = "flex";
  mensajeDespedida.style.flexDirection = "column";
  mensajeDespedida.style.alignItems = "center";
  mensajeDespedida.style.gap = "10px";
  mensajeDespedida.style.textAlign = "center";

  // Texto
  const texto = document.createElement("p");
  texto.textContent = windowConfig.inp_despedida || "¡Gracias por usar nuestro asistente virtual!";
  texto.style.margin = 0;
console.log(windowConfig.inp_despedida);

  // Logo
  const logo = document.createElement("img");
  logo.src = windowConfig.urlLogotipo || "";
  logo.alt = "Logo Chatbot";
  logo.style.width = "60px";
  logo.style.height = "60px";

  mensajeDespedida.appendChild(texto);
  mensajeDespedida.appendChild(logo);

  contenedor.appendChild(mensajeDespedida);
  contenedor.scrollTop = contenedor.scrollHeight;

  mensajeDespedida.scrollIntoView({ behavior: "smooth" });

  // Luego cerrar chatbot después de unos segundos
  setTimeout(cerrar, 3500);
}

// MODIFICACIÓN extra: Función para mostrar confirmación después del chat IA
function mostrarConfirmacionAyudaIA() {
  const contenedor = document.querySelector(".chatbot-body");

  // Crear contenedor de mensaje manualmente
  const divMensaje = document.createElement("div");
  divMensaje.className = "chatbot-message";
  divMensaje.innerHTML = "<p>¿Puedo ayudarte con algo más?</p>";

  // Crear contenedor de botones
  const botonesDiv = document.createElement("div");
  botonesDiv.className = "chatbot-message-buttons";
  botonesDiv.style.marginTop = "20px";

  // Botón "Sí"
  const btnSi = document.createElement("button");
  btnSi.textContent = "Sí";
  btnSi.className = "btnSi";
  btnSi.style.marginRight = "5px";
  btnSi.style.borderRadius = "15px";
  btnSi.style.padding = "4px 12px";

  // Botón "No"
  const btnNo = document.createElement("button");
  btnNo.textContent = "No";
  btnNo.className = "btnNo";
  btnNo.style.borderRadius = "15px";
  btnNo.style.padding = "4px 10px";

  // Agregar botones al div
  botonesDiv.appendChild(btnSi);
  botonesDiv.appendChild(btnNo);

  // Agregar botones al mensaje
  divMensaje.appendChild(botonesDiv);

  // Insertar mensaje en el chat
  contenedor.appendChild(divMensaje);
  divMensaje.scrollIntoView({ behavior: "smooth" });

  // Aplicar estilos a los botones
  aplicarEstilosBotones(divMensaje);

  // Listeners de botones
  btnSi.addEventListener("click", () => {
    funcionSi();
    bloquearBotones(botonesDiv);
  });

  btnNo.addEventListener("click", () => {
    funcionNo();
    bloquearBotones(botonesDiv);
  });
}

document.addEventListener("DOMContentLoaded", function () {
  const aiInput = document.getElementById("ai-input");
  const aiSendBtn = document.getElementById("ai-send-btn");
  const chatBox = document.getElementById("chatBox");
  const sessionContainer = document.getElementById("chatbot-container");

  // Solo aplica para el chat IA
  if (aiSendBtn && aiInput) {
    aiSendBtn.addEventListener("click", async () => {
      let sessionId = sessionContainer?.dataset?.sessionId;

      // Crear nueva sesión si no existe
      if (!sessionId) {
        try {
          const res = await fetch("http://localhost:8000/api/start/", { method: "GET" });
          if (!res.ok) throw new Error(await res.text());

          const finalUrl = res.url;
          const match = finalUrl.match(/\/chat\/(\d+)\//);
          if (match) {
            sessionId = match[1];
            sessionContainer.dataset.sessionId = sessionId;
          } else {
            console.error("No se pudo obtener sessionId de la redirección.");
            return;
          }
        } catch (error) {
          console.error("Error creando sesión de chat:", error);
          return;
        }
      }

      const userMessage = aiInput.value.trim();
      if (!userMessage) return;

      // Mostrar mensaje del usuario
      const userBubble = document.createElement("div");
      userBubble.classList.add("chatbot-message", "user-message");
      userBubble.innerHTML = `<p>${userMessage}</p>`;
      chatBox.appendChild(userBubble);
      chatBox.scrollTop = chatBox.scrollHeight;
      aiInput.value = "";

      // Enviar mensaje a la API de IA
      try {
        const response = await fetch(`http://localhost:8000/api/chat/${sessionId}/send/`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ message: userMessage }),
        });

        if (!response.ok) {
          const errorText = await response.text();
          console.error("Error en respuesta:", errorText);
          return;
        }

        const data = await response.json();

        // Mostrar respuesta del bot IA
        const botBubble = document.createElement("div");
        botBubble.classList.add("chatbot-message");
        botBubble.innerHTML = `<p>${data.reply}</p>`;
        chatBox.appendChild(botBubble);
        chatBox.scrollTop = chatBox.scrollHeight;

        // MOSTRAR CONFIRMACIÓN DESPUÉS DE LA RESPUESTA DE IA
        setTimeout(mostrarConfirmacionAyudaIA, 500);

      } catch (error) {
        console.error("Error al consultar la IA:", error);
      }
    });
  }

  // El chat normal se maneja con su propio botón y función:
  // <button onclick="enviarRespuesta()">Enviar</button>
  // No se toca ni se intercepta aquí.
});



document.addEventListener("DOMContentLoaded", () => {
  const aiChat = document.getElementById("ai-chat-container");
  const userChat = document.getElementById("user-input-container");

  // Al iniciar, solo la IA visible
  aiChat.style.display = "flex";
  userChat.style.display = "none";
});

// --- Cuando se selecciona el tema de seguimiento ---
function activarCajaSeguimiento() {
  const aiChat = document.getElementById("ai-chat-container");
  const userChat = document.getElementById("user-input-container");
  aiChat.style.display = "none";
  userChat.style.display = "flex";
}

// --- Cuando se vuelve al chat normal o se cierra ---
function activarCajaIA() {
  const aiChat = document.getElementById("ai-chat-container");
  const userChat = document.getElementById("user-input-container");
  aiChat.style.display = "flex";
  userChat.style.display = "none";
}