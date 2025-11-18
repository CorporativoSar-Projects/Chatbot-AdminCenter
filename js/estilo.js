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


const txtNombreChat = document.querySelector('#inp_nombre');
const divCopiaNombre = document.getElementById('txt-titulo-chat');

document.addEventListener("DOMContentLoaded", () => {
    const nombreGuardado = localStorage.getItem('inp_nombre');
    if (nombreGuardado) {
        txtNombreChat.value = nombreGuardado;
        divCopiaNombre.innerHTML = nombreGuardado;
    }
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
    const logoURL = urlLogotipoInput || localStorage.getItem("chatbotLogo") || "img/logochiquito.png";


    localStorage.setItem("chatbotLogo", logoURL);
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
