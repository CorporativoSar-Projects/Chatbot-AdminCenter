document.addEventListener("DOMContentLoaded", function () {
    const inputs = [
        { id: 'colorPrimario', defaultValue: '#e39842' },
        { id: 'colorSecundario', defaultValue: '#b6b6b6' },
        { id: 'colorAcento', defaultValue: '#383838' },
        { id: 'colorTexto', defaultValue: '#000000' },
        { id: 'colorRespuestaUsuario', defaultValue: '#219ebc' }
    ];

    inputs.forEach(inputData => {
        const inputElement = document.getElementById(inputData.id);
        const muestraElement = document.getElementById(`muestra${capitalize(inputData.id)}`);

      const storedValue = localStorage.getItem(inputData.id) || inputData.defaultValue;
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
    localStorage.setItem('colorTexto', colors['--color-texto']);
    localStorage.setItem('colorAcento', colors['--color-acento']);
    localStorage.setItem('colorTexto', colors['--color-texto']);
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
    
//DOM almacenar los datos en el local storage 
document.addEventListener("DOMContentLoaded", function () {
    const btnGuardar = document.getElementById("btnGuardarEstilo");

    btnGuardar.addEventListener("click", function (event) {
        event.preventDefault();

      //Objeto con el que los datos se van a guardar
        const datosEstilo = {
           "inp_nombre": document.getElementById('inp_nombre').value,
            colorPrimario: document.getElementById('colorPrimario').value,
            colorSecundario: document.getElementById('colorSecundario').value,
            colorTexto: document.getElementById('colorTexto').value,
            colorAcento: document.getElementById('colorAcento').value,
            colorUsuario: document.getElementById('colorRespuestaUsuario').value,
            urlLogotipo: document.getElementById('urlLogotipo').value
        };

        // Envía los datos al archivo PHP mediante fetch
    fetch('modelo/guardar_chatbot.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            seccion: 'estilo', // sección en la que estan guardando lo datos
            datos: datosEstilo
        })
    })
    .then(response => response.json())
    .catch(error => console.error("Error en fetch:", error));
       });
    });
