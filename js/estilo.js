document.addEventListener("DOMContentLoaded", function () {
    const inputs = [
        { id: 'colorPrimario', defaultValue: '#3ca6e5' },
        { id: 'colorSecundario', defaultValue: '#b6b6b6' },
        { id: 'colorAcento', defaultValue: '#383838' },
        { id: 'colorTexto', defaultValue: '#000000' },
        { id: 'colorRespuestaUsuario', defaultValue: '#219ebc' }
    ];
    

   inputs.forEach(inputData => {
    const inputElement = document.getElementById(inputData.id);
    const muestraElement = document.getElementById(`muestra${capitalize(inputData.id)}`);

    let storedValue = localStorage.getItem(inputData.id);

    if (!storedValue) {
       
        storedValue = inputElement.value || inputData.defaultValue;
        localStorage.setItem(inputData.id, storedValue);
    }

    
    inputElement.value = storedValue;
    muestraElement.style.backgroundColor = storedValue;

    inputElement.addEventListener("input", function () {
        const color = inputElement.value;
        if (isValidHex(color)) {
            muestraElement.style.backgroundColor = color;
            localStorage.setItem(inputData.id, color);
           actualizarColores();
        }
    });
    
});
actualizarColores();
});

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function isValidHex(color) {
    const hexRegex = /^#([0-9A-F]{3}){1,2}$/i;
    return hexRegex.test(color);
}

function actualizarColores() {
    const colors = {
        '--color-primario': document.getElementById('colorPrimario').value,
        '--color-secundario': document.getElementById('colorSecundario').value,
        '--color-acento': document.getElementById('colorAcento').value,
        '--color-texto': document.getElementById('colorTexto').value,
        '--color-texto-boton': document.getElementById('colorTexto').value,
        '--color-respuesta-usuario': document.getElementById('colorRespuestaUsuario').value,
    };

    for (const [key, value] of Object.entries(colors)) {
        if (isValidHex(value)) {
            document.documentElement.style.setProperty(key, value);
        }
    }

    localStorage.setItem('colorPrimario', colors['--color-primario']);
    localStorage.setItem('colorSecundario', colors['--color-secundario']);
    localStorage.setItem('colorTexto', colors['--color-texto']);
    localStorage.setItem('colorAcento', colors['--color-acento']);
    localStorage.setItem('colorTexto', colors['--color-texto']);
    localStorage.setItem('colorTexto', colors['--color-texto-boton']);
    localStorage.setItem('colorRespuestaUsuario', colors['--color-respuesta-usuario']);
}


// ==========================
// FUNCIÓN PARA NORMALIZAR LA URL DEL LOGO
// ==========================
function normalizarURLImagen(url) {
    if (!url) return "";

    url = url.trim();

    // Si NO empieza con http:// o https:// se agrega https://
    if (!/^https?:\/\//i.test(url)) {
        url = "https://" + url;
    }

    return url;
}

// Función que comprueba si la imagen realmente carga
function verificarImagen(url) {
    return new Promise((resolve, reject) => {
        const img = new Image();
        img.onload = () => resolve(true);
        img.onerror = () => reject(false);
        img.src = url;
    });
}

const contador = document.getElementById('contadorSty');
 
// Función para actualizar el contador
function actualizarContador() {
    const max = txtNombreChat.getAttribute('maxlength');
    contador.textContent = `${txtNombreChat.value.length} / ${max}`;
}

// ---------- PREVISUALIZAR IMAGEN ----------
function previsualizarImagen() {
    const urlInput = document.getElementById('urlLogotipo');
    const chatbotIcon = document.getElementById('chatbotIcon');
    let logoURL = normalizarURLImagen(urlInput.value.trim());

    if (logoURL !== '') {
        verificarImagen(logoURL)
            .then(() => {
                chatbotIcon.src = logoURL;
                localStorage.setItem('chatbotLogo', logoURL);
            })
            .catch(() => {
                chatbotIcon.removeAttribute('src');
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cargar el logotipo',
                    text: 'No se pudo cargar la imagen desde la URL proporcionada. Verifica el enlace.',
                    confirmButtonColor: '#ffb703'
                });
            });
    } else {
        chatbotIcon.removeAttribute('src');
        Swal.fire({
            icon: 'warning',
            title: 'Sin logotipo',
            text: 'Por favor, ingresa una URL para el logotipo.',
            confirmButtonColor: '#ffb703'
        });
    }
}


// Cambio en tiempo real del logo al escribir la URL
document.getElementById("urlLogotipo").addEventListener("input", function () {
    const rawURL = this.value.trim();
    const chatbotIcon = document.getElementById("chatbotIcon");

      if (rawURL === "") {
        chatbotIcon.removeAttribute("src");
        return;
    }

   // Normalizar solo para previsualizar, NO sobreescribe el input
    const urlNormalizada = normalizarURLImagen(rawURL);


    verificarImagen(urlNormalizada)
        .then(() => {
            chatbotIcon.src = urlNormalizada;
            localStorage.setItem("chatbotLogo", urlNormalizada);
        })
        .catch(() => {
            chatbotIcon.removeAttribute("src");
        });
});

// Normalizar cuando el usuario termina de escribir
document.getElementById("urlLogotipo").addEventListener("blur", function () {
    if (this.value.trim() === "") return;
    this.value = normalizarURLImagen(this.value.trim());
});

const txtNombreChat = document.querySelector('#inp_nombre');
const divCopiaNombre = document.getElementById('txt-titulo-chat');

document.addEventListener("DOMContentLoaded", () => {
    const nombreGuardado = localStorage.getItem('inp_nombre');
    if (nombreGuardado) {
        txtNombreChat.value = nombreGuardado;
        divCopiaNombre.innerHTML = nombreGuardado;
   }
    actualizarContador();

    const logoURL = localStorage.getItem('chatbotLogo');
    if (logoURL) {
        const urlInput = document.getElementById('urlLogotipo');
        const chatbotIcon = document.getElementById('chatbotIcon');

        urlInput.value = logoURL;        
        chatbotIcon.src = logoURL;       
    }

});

txtNombreChat.addEventListener('keyup', () => {
    const nombre = txtNombreChat.value;
    divCopiaNombre.innerHTML = nombre;
    localStorage.setItem('inp_nombre', nombre);
    actualizarContador();
});

// Función para previsualizar la imagen desde url
function previsualizarImagen() {
    const urlInput = document.getElementById('urlLogotipo');
    const chatbotIcon = document.getElementById('chatbotIcon');
    
        if (urlInput.value.trim() !== '') {
            const logoURL = urlInput.value;
    
            // Guardar la URL en localStorage
            localStorage.setItem('chatbotLogo', logoURL);
    
            // Cambiar el logo en la página actual
            chatbotIcon.src = logoURL;
        } else {
            chatbotIcon.src = 'img/logochiquito.png'; // Imagen por defecto
        }
}
    
document.getElementById("btnGuardarEstilo").addEventListener("click", function () {
    const id_chatbot = localStorage.getItem("id_chatbot") || null;

     // Tomar primero la URL actual del input o el valor de localStorage o la imagen por defecto
    const urlLogotipoInput = document.getElementById("urlLogotipo").value.trim();
    const logoPrevio = localStorage.getItem("chatbotLogo") || "";
    const logoURL = urlLogotipoInput !== "" ? urlLogotipoInput : logoPrevio;

   // Solo guardar si el usuario escribió una nueva URL
        if (urlLogotipoInput !== "") {
            logoURL = urlLogotipoInput;
            localStorage.setItem("chatbotLogo", logoURL);
        }

    //Objeto con el que los datos se van a guardar
    const data = {
        inp_nombre: document.getElementById("inp_nombre").value,
        colorPrimario: localStorage.getItem("colorPrimario") || "",
        colorSecundario: localStorage.getItem("colorSecundario") || "",
        colorTexto: localStorage.getItem("colorTexto") || "",
        colorAcento: localStorage.getItem("colorAcento") || "",
        colorUsuario: localStorage.getItem("colorRespuestaUsuario") || "",
        urlLogotipo: logoURL
    };

    // Si ya existe un id_chatbot, se incluye para hacer actualización
    if (id_chatbot) {
        data.id_chatbot = parseInt(id_chatbot);
    }

    fetch("modelo/guardar_datos.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            seccion: "estilo", // Seccion donde se almacenaran los datos
            datos: data
        })
    })
    .then(response => response.json()) // Convierte la respuesta a formato JSON
    .then(res => {

    if (res.success) {
        if (res.id_chatbot) { //si la respuesta es exitosa
            localStorage.setItem("id_chatbot", res.id_chatbot); //Se devuelve el id
             Swal.fire({
          icon: 'success',
          title: 'Estilo guardado',
          text: 'El estilo del chatbot se guardó correctamente.',
          confirmButtonColor: '#ffb703'
        }); 
    }
    } else {
        alert("Error: " + res.error);
    }
})
.catch(err => {
     console.error("Error al guardar en base de datos", err);
        alert("Error de red o del servidor.");
});
    
});
