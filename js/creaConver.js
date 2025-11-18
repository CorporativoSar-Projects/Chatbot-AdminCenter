// --- Inicializar localStorage con datosChatbot al cargar la página ---
document.addEventListener("DOMContentLoaded", function () {
    const campos = [
        "inp_mensaje_usuario", "inp_columna", "inp_url_informe",
        "inp_mensaje_usuario2", "inp_columna2",
        "inp_mensaje_usuario3", "inp_columna3", "inp_url_informe3"
    ];

    campos.forEach(campo => {
        // Solo copia a localStorage si aún no existe
        if (!localStorage.getItem(campo)) {
            localStorage.setItem(campo, datosChatbot[campo] || "");
        }
    });
});



// Función para manejar el estado activo de los botones
function handleButtonClick(event) {
  // Remover la clase 'active' de todos los botones
  var buttons = document.querySelectorAll(".boton1");
  buttons.forEach((button) => button.classList.remove("active"));

  // Añadir la clase 'active' al botón presionado
  event.target.classList.add("active");
}

function impMenu1(event) {
  handleButtonClick(event);
  // Contenido de la función impMenu1
  var stringMenu = `
        <div class="container-crear-conver">
            <label class="label-nombrechat2">Mensaje inicial de la conversación<span class="false-span" style="color: white;">1234567891011121314151617181920</span></label><br>
            <input type="text" name="inp_mensaje_usuario" id="inp_mensaje_usuario" placeholder="Escribe"
                class="inp-mensaje-usuario-crear" required><br>
            
            <label class="label-nombrechat">Origen de búsqueda</label><br>
            <input type="text" name="inp_columna" id="inp_columna" placeholder="Escribe"
                class="input-columna-crear" required ><br>

            <label class="label-nombrechat">URL del informe</label><br>
            <input type="url" name="inp_url_informe" id="inp_url_informe" placeholder="https://ejemplo.csv"
                    class="input-columna-crear"><br>

            
        </div>

    `;
  document.getElementById("imprimir").innerHTML = stringMenu;

  //Recupera los datos del localStogare
    document.getElementById("inp_mensaje_usuario").value = localStorage.getItem("inp_mensaje_usuario") || datosChatbot.inp_mensaje_usuario || "";
  document.getElementById("inp_columna").value = localStorage.getItem("inp_columna") || datosChatbot.inp_columna || "";
  document.getElementById("inp_url_informe").value = localStorage.getItem("inp_url_informe") || datosChatbot.inp_url_informe || "";

  //Se movio el bloque de código comentado de la url porque el marcado que devuelve js no permite comentarios

  /* <label class="label-nombrechat">URL de origen de datos</label><br>
            <input type="text" name="inp-columna" id="inp-url" placeholder="Escribe"
                class="input-columna-crear" required><br> */
}

function impMenu2(event) {
  handleButtonClick(event);
  // Contenido de la función impMenu2
  var stringMenu = `
        <div class="container-crear-conver">
            <label class="label-nombrechat2">Mensaje inicial de la conversación<span class="false-span" style="color: white;">1234567891011121314151617181920</span></label><br>
            <input type="text" name="inp_mensaje_usuario2" id="inp_mensaje_usuario2" placeholder="Escribe"
                class="inp-mensaje-usuario-crear" required><br>
          
            <label class="label-nombrechat">Origen de búsqueda</label><br>
            <input type="text" name="inp_columna2" id="inp_columna2" placeholder="Escribe"
                class="input-columna-crear" required><br>
        </div>
    `;
  document.getElementById("imprimir").innerHTML = stringMenu;
  //Recupera los datos del localStorage
   document.getElementById("inp_mensaje_usuario2").value = localStorage.getItem("inp_mensaje_usuario2") || datosChatbot.inp_mensaje_usuario2 || "";
  document.getElementById("inp_columna2").value = localStorage.getItem("inp_columna2") || datosChatbot.inp_columna2 || "";
  //Se movio el bloque de código comentado de la url porque el marcado que devuelve js no permite comentarios

  /*   <label class="label-nombrechat">URL de origen de datos</label><br>
            <input type="text" name="inp-columna" id="inp-url" placeholder="Escribe"
                class="input-columna-crear" required><br> */
}

function impMenu3(event) {
  handleButtonClick(event);
  // Contenido de la función impMenu3
  var stringMenu = `
        <div class="container-crear-conver">
            <label class="label-nombrechat2">Mensaje inicial de la conversación<span class="false-span" style="color: white;">1234567891011121314151617181920</span></label><br>
            <input type="text" name="inp_mensaje_usuario3" id="inp_mensaje_usuario3" placeholder="Escribe"
                class="inp-mensaje-usuario-crear" required><br>
           
            
            <label class="label-nombrechat">Origen de búsqueda</label><br>
            <input type="text" name="inp_columna3" id="inp_columna3" placeholder="Escribe"
                class="input-columna-crear" required><br>
      
            <label class="label-nombrechat">URL del informe</label><br>
            <input type="url" name="inp_url_informe3" id="inp_url_informe3" placeholder="https://ejemplo.com"
                    class="input-columna-crear"><br>
                     
        </div>
    `;
  document.getElementById("imprimir").innerHTML = stringMenu;

  //Recupera los datos del localStogare
    document.getElementById("inp_mensaje_usuario3").value = localStorage.getItem("inp_mensaje_usuario3") || datosChatbot.inp_mensaje_usuario3 || "";
  document.getElementById("inp_columna3").value = localStorage.getItem("inp_columna3") || datosChatbot.inp_columna3 || "";
  document.getElementById("inp_url_informe3").value = localStorage.getItem("inp_url_informe3") || datosChatbot.inp_url_informe3 || "";
  //Se movio el bloque de código comentado de la url porque el marcado que devuelve js no permite comentarios

  /* <label class="label-nombrechat">URL de origen de datos</label><br>
            <input type="text" name="inp-columna" id="inp-url" placeholder="Escribe"
                class="input-columna-crear" required><br> */
}

// Añadir eventos a los botones
document.getElementById("boton1").addEventListener("click", impMenu1);
document.getElementById("boton2").addEventListener("click", impMenu2);
document.getElementById("boton3").addEventListener("click", impMenu3);

// Guarda automáticamente en localStorage cuando se escriben los campos
document.addEventListener("input", function (e) {
  //si es inp-mensaje-usuario se guarda en el localStorage 
  if (e.target.id === "inp_mensaje_usuario") {
    localStorage.setItem("inp_mensaje_usuario", e.target.value);
    // Si el input tiene el Id 'inp_columna', se guarda en localStorage.
  } else if (e.target.id === "inp_columna") {
    localStorage.setItem("inp_columna", e.target.value);
    // Si el input tiene el ID 'inp_url_informe', se guarda su valor.
  } else if (e.target.id === "inp_url_informe") {
    localStorage.setItem("inp_url_informe", e.target.value);
  }

  if (e.target.id === "inp_mensaje_usuario2") {
    localStorage.setItem("inp_mensaje_usuario2", e.target.value);
  } else if (e.target.id === "inp_columna2") {
    localStorage.setItem("inp_columna2", e.target.value);
  } 

  if (e.target.id === "inp_mensaje_usuario3") {
    localStorage.setItem("inp_mensaje_usuario3", e.target.value);
  } else if (e.target.id === "inp_columna3") {
    localStorage.setItem("inp_columna3", e.target.value);
  } else if (e.target.id === "inp_url_informe3") {
    localStorage.setItem("inp_url_informe3", e.target.value);
  }
});


function validarURL(valor) {
    if (!valor) return true; // Permite campos vacíos, si no son obligatorios

    try {
        const url = new URL(valor);
        return url.protocol === "http:" || url.protocol === "https:";
    } catch (_) {
        return false;
    }
}

document.getElementById("btnGuardarConver").addEventListener("click", function () {
    // Recuperar id_chatbot desde localStorage
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


    // Validar URLs antes de guardar
    const url1 = localStorage.getItem("inp_url_informe") || "";
    const url3 = localStorage.getItem("inp_url_informe3") || "";

    if (!validarURL(url1)) {
        alert("La URL del informe del tema 1 no es válida.");
        return;
    }

    if (!validarURL(url3)) {
        alert("La URL del informe del tema 3 no es válida.");
        return;
    }

     //Objeto con el que los datos se van a guardar
    const datosConver = {
        id_chatbot: parseInt(id_chatbot), // importante para asociarlo correctamente
        inp_mensaje_usuario: localStorage.getItem("inp_mensaje_usuario") || "",
        inp_columna: localStorage.getItem("inp_columna") || "",
        inp_url_informe: url1,
        inp_mensaje_usuario2: localStorage.getItem("inp_mensaje_usuario2") || "",
        inp_columna2: localStorage.getItem("inp_columna2") || "",
        inp_mensaje_usuario3: localStorage.getItem("inp_mensaje_usuario3") || "",
        inp_columna3: localStorage.getItem("inp_columna3") || "",
        inp_url_informe3: url3
    };

    // Envía los datos al archivo PHP mediante fetch
    fetch("modelo/guardar_datos.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            seccion: "conversacion", // Seccion donde se almacenaran los datos
            datos: datosConver
        })
    })
    .then(response => response.json()) // Convierte la respuesta a formato JSON
    /*.then(response => {
        if (response.success) {
            alert("Conversación guardada correctamente.");
        } else {
            console.error("Error en el backend:", response.error);
            alert("Error al guardar la conversación: " + response.error);
        }
    })*/
    .catch(err => {
        console.error("Error al guardar en base de datos", err);
      alert("Error de red o del servidor.");
    });
});


