document.addEventListener("DOMContentLoaded", function () {
    const inputs = [
        { id: 'colorPrimario', defaultValue: '#e39842' },
        { id: 'colorSecundario', defaultValue: '#b6b6b6' },
        { id: 'colorAcento', defaultValue: '#383838' },
        { id: 'colorTexto', defaultValue: '#000000' }
    ];

    inputs.forEach(inputData => {
        const inputElement = document.getElementById(inputData.id);
        const muestraElement = document.getElementById(`muestra${capitalize(inputData.id)}`);

        muestraElement.style.backgroundColor = inputElement.value;

        inputElement.addEventListener("input", function () {
            const color = inputElement.value;
            if (isValidHex(color)) {
                muestraElement.style.backgroundColor = color;
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
    };

    for (const [key, value] of Object.entries(colors)) {
        if (isValidHex(value)) {
            document.documentElement.style.setProperty(key, value);
        }
    }
}


const txtNombreChat = document.querySelector('#inp-nombre');
const divCopiaNombre = document.getElementById('txt-titulo-chat');

txtNombreChat.addEventListener('keyup', () => {
    divCopiaNombre.innerHTML = txtNombreChat.value;
});

    // Función para previsualizar la imagen
    function previsualizarImagen() {
        const urlInput = document.getElementById('urlLogotipo');
        const chatbotIcon = document.getElementById('chatbotIcon');

        // Verifica si el campo no está vacío
        if (urlInput.value.trim() !== '') {
            chatbotIcon.src = urlInput.value; // Cambia el logo a la URL ingresada
        } else {
            chatbotIcon.src = 'img/logochiquito.png'; // Imagen por defecto
        }
    }

    // Detectar cuando se presiona Enter
    document.getElementById('urlLogotipo').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            previsualizarImagen(); // Llamar a la función de previsualización cuando se presiona Enter
        }
    });


