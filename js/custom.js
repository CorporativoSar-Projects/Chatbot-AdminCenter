document.addEventListener("DOMContentLoaded", function() {
    const colorInputs = document.querySelectorAll("input[type='color']");
    
    colorInputs.forEach(input => {
        const muestra = document.querySelector(`#muestra${input.id.charAt(0).toUpperCase() + input.id.slice(1)}`);
        muestra.style.backgroundColor = input.value;

        input.addEventListener("input", function() {
            muestra.style.backgroundColor = input.value;
        });
    });
});



function actualizarColores() {
    // Obtener los valores de color seleccionados
    const colorPrimario = document.getElementById('colorPrimario').value;
    const colorSecundario = document.getElementById('colorSecundario').value;
    const colorAcento = document.getElementById('colorAcento').value;
    const colorTexto = document.getElementById('colorTexto').value;
    

    //  CSS
    document.documentElement.style.setProperty('--color-primario', colorPrimario);
    document.documentElement.style.setProperty('--color-secundario', colorSecundario);
    document.documentElement.style.setProperty('--color-acento', colorAcento);
    document.documentElement.style.setProperty('--color-texto', colorTexto);
    

    // Ajustar colores específicos si es necesario
    document.documentElement.style.setProperty('--color-texto-header', '#ffffff'); // Texto del header siempre blanco
    document.documentElement.style.setProperty('--color-texto-boton', colorTexto); // Texto de los botones
    document.documentElement.style.setProperty('--color-texto-hover', '#ffffff'); // Texto de los botones al pasar el ratón
}

function actualizarColoresBurbuja(){
    const colorPrimarioBurbuja = document.getElementById('colorPrimarioBurbuja').value;
    document.documentElement.style.setProperty('--colorPrimarioBurbuja', colorPrimarioBurbuja);
}

const txtNombreChat = document.querySelector('#inp-nombre');
const divCopiaNombre = document.getElementById('txt-titulo-chat');

txtNombreChat.addEventListener('keyup', () => {
    divCopiaNombre.innerHTML = txtNombreChat.value;
});

function previsualizarImagen() {
    const archivoInput = document.getElementById('archivoLogotipo');
    const archivo = archivoInput.files[0];
    const nombreArchivo = document.getElementById('nombreArchivo');
    const chatbotIcon = document.querySelector('.chatbot-icon');

    if (archivo) {
        // Actualizar el nombre del archivo
        nombreArchivo.textContent = archivo.name;

        // Crear una URL para la imagen cargada
        const lector = new FileReader();
        lector.onload = function(e) {
            // Establecer la imagen cargada como la fuente del icono del chatbot
            chatbotIcon.src = e.target.result;
        }
        lector.readAsDataURL(archivo);
    } else {
        nombreArchivo.textContent = 'Seleccione un archivo';
        chatbotIcon.src = 'img/logochiquito.png'; // Imagen por defecto
    }
}


