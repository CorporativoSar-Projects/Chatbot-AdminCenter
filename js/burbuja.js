
document.addEventListener("DOMContentLoaded", function() {
    const inputs = [
        { id: 'colorPrimarioBurbuja', defaultValue: '#e39842' },
        { id: 'colorTextoBurbuja', defaultValue: '#000000' }
    ];

    inputs.forEach(inputData => {
        const inputElement = document.getElementById(inputData.id);
        const muestraElement = document.getElementById(`muestra${capitalize(inputData.id)}`);

        //clave que se uso para el localStorage
        const colorStorageKey = inputData.id.includes("Primario") ? 'colorPrimario' : 'colorTexto';
       //Recupera el color que esta guardado en el almacenamiento
        const savedColor = localStorage.getItem(colorStorageKey);

        const colorToUse = isValidHex(savedColor) ? savedColor : inputData.defaultValue;

        inputElement.value = colorToUse; 
        muestraElement.style.backgroundColor = colorToUse;

        actualizarColores();

        inputElement.disabled = true;

        inputElement.addEventListener("input", function() {
            const color = inputElement.value;
            if (isValidHex(color)) {
                muestraElement.style.backgroundColor = color;
                actualizarColores();
            }
        });
    });

    //Ayuda a eliminar los colores que se guardaron en localstorage
 //window.addEventListener('beforeunload', function() {
       // localStorage.removeItem('colorPrimario');
       // localStorage.removeItem('colorTexto');
    //});
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
        '--colorPrimarioBurbuja': document.getElementById('colorPrimarioBurbuja').value,
        '--colorTextoBurbuja': document.getElementById('colorTextoBurbuja').value,
    };

    for (const [key, value] of Object.entries(colors)) {
        if (isValidHex(value)) {
            document.documentElement.style.setProperty(key, value);
        }
    }
}

const txtBurbuja = document.querySelector('#inp_burbuja');
const divCopiaBurb = document.getElementById('chatTextBurb');
const contador = document.getElementById('contadorBurbu');
 
// Función para actualizar el contador
function actualizarContador() {
    const max = txtBurbuja.getAttribute('maxlength');
    contador.textContent = `${txtBurbuja.value.length} / ${max}`;
}

txtBurbuja.addEventListener('keyup', () => {
    divCopiaBurb.innerHTML = txtBurbuja.value;
    localStorage.setItem('inp_burbuja', txtBurbuja.value);
    actualizarContador();
});

//codigo que llama el logo desde el localStorage
document.addEventListener('DOMContentLoaded', function () {
    const bubbleIcon = document.getElementById('chatBubbleIcon');
   
    // Revisar si hay un logo guardado en localStorage
    const savedLogo = localStorage.getItem('chatbotLogo');

    if (savedLogo) {
        bubbleIcon.src = savedLogo; // Aplicar el logo guardado
    }

     const savedBurbuja = localStorage.getItem('inp_burbuja');

    if (savedBurbuja) {
        document.getElementById('inp_burbuja').value = savedBurbuja;
        document.getElementById('chatTextBurb').innerHTML = savedBurbuja;
        
    }

    actualizarContador();
    // Al recargar la página, eliminar la URL guardada
   // window.addEventListener('beforeunload', function () {
       // localStorage.removeItem('chatbotLogo');
    //});
});

//DOM 
/*document.addEventListener("DOMContentLoaded", function () {
    const btnGuardar = document.getElementById("btnGuardarBurbuja");

    btnGuardar.addEventListener("click", function (event) {
        event.preventDefault();

        // Recuperar ID del chatbot 
        const id_chatbot = localStorage.getItem("id_chatbot");
        if (!id_chatbot) {
           Swal.fire({
        icon: 'warning',
        title: 'Atención',
        text: 'Para generar el chatbot necesitas al menos guardar la configuración de estilo.',
        confirmButtonText: 'Entendido',
        showCloseButton: true,
        confirmButtonColor: '#ffb703'

  });
  return;
        }

        //Objeto con el que los datos se van a guardar
        const datosBurbuja = {
            id_chatbot: parseInt(id_chatbot),
            inp_burbuja: document.getElementById('inp_burbuja').value
        };

         // Envía los datos al archivo PHP mediante fetch
        fetch('modelo/guardar_datos.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json' 
            },
            body: JSON.stringify({
                seccion: 'burbuja', // Seccion donde se almacenaran los datos
                datos: datosBurbuja
            })
        })
        .then(response => response.json()) // Convierte la respuesta a formato JSON
        .catch(err => {
             console.error("Error al guardar en base de datos", err);
            alert("Error de red o del servidor.");
        })
    });
});*/
