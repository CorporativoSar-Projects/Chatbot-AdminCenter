
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

txtBurbuja.addEventListener('keyup', () => {
    divCopiaBurb.innerHTML = txtBurbuja.value;
    localStorage.setItem('nombreBurbuja', txtBurbuja.value);
});

//codigo que llama el logo desde el localStorage
document.addEventListener('DOMContentLoaded', function () {
    const bubbleIcon = document.getElementById('chatBubbleIcon');
    const nombreBurbuja = localStorage.getItem('nombreBurbuja');

    // Revisar si hay un logo guardado en localStorage
    const savedLogo = localStorage.getItem('chatbotLogo');

    if (savedLogo) {
        bubbleIcon.src = savedLogo; // Aplicar el logo guardado
    }

     if (nombreBurbuja) {
        document.getElementById('inp_burbuja').value = nombreBurbuja;
        document.getElementById('chatTextBurb').innerHTML = nombreBurbuja;
    }

    // Al recargar la página, eliminar la URL guardada
   // window.addEventListener('beforeunload', function () {
       // localStorage.removeItem('chatbotLogo');
    //});
});
//DOM 
document.addEventListener("DOMContentLoaded", function () {
    const btnGuardar = document.getElementById("btnGuardarBurbuja");

    btnGuardar.addEventListener("click", function (event) {
        event.preventDefault();

        //Objeto con el que el dato se va a guardar
        const datosBurbuja = {
            "inp_burbuja": document.getElementById('inp_burbuja').value
        };
        // Envía los datos al archivo PHP mediante fetch
        fetch('modelo/guardar_chatbot.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json' 
            },
            body: JSON.stringify({
                seccion: 'burbuja', //Sección en la que se almacenan los datos
                datos: datosBurbuja
            })
        })
        .then(response => response.json()) //// Convierte la respuesta a formato JSON
        //.then(result => {
            //if (result.success) {
                //console.log("Sección 'burbuja' guardada correctamente en sesión");
          //  } else {
             //   console.error("Error al guardar burbuja:", result.error);
           // }
       // })
        .catch(error => console.error("Error en fetch:", error));
    });
});
