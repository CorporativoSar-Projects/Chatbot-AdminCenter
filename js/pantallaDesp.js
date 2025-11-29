// Seleccionar el input y el contenedor del texto del chatbot
const txtDespedida = document.querySelector('#inp_despedida');
const divCopiaDesp= document.getElementById('txt-chatbot-Desp');
const contador = document.getElementById('contadorCaracteres');

// Función para actualizar el contador
function actualizarContador() {
    const max = txtDespedida.getAttribute('maxlength');
    contador.textContent = `${txtDespedida.value.length} / ${max}`;
}

document.addEventListener('DOMContentLoaded', () => {
    // Cargar valor desde localStorage si existe
    const despedidaGuardada = localStorage.getItem('inp_despedida');

    if (despedidaGuardada) {
        txtDespedida.value = despedidaGuardada;
        divCopiaDesp.innerHTML = despedidaGuardada;
    }

    // Asegurar que el contador refleja el valor inicial
    actualizarContador();

    // Colores de iconos
    const colorTexto = localStorage.getItem('colorTexto') || '#000000';
    const iconosSVG = document.querySelectorAll('.chatbot-min svg, .chatbot-close svg');

    iconosSVG.forEach(svg => {
        svg.style.stroke = colorTexto;
        svg.style.fill = colorTexto;
    });
});

// Actualizar preview y contador cuando escribe
txtDespedida.addEventListener('input', () => {
    divCopiaDesp.innerHTML = txtDespedida.value;
    localStorage.setItem('inp_despedida', txtDespedida.value);
    actualizarContador();
});


/*document.getElementById("btnGuardarDespedida").addEventListener("click", function () {
    // Validar que haya un id_chatbot guardado
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
    const datosDespedida = {
      id_chatbot: parseInt(id_chatbot),
      inp_despedida: document.getElementById('inp_despedida').value
    };

    // Envía los datos al archivo PHP mediante fetch
    fetch("modelo/guardar_datos.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        seccion: "despedida", // Seccion donde se almacenaran los datos
        datos: datosDespedida
      })
    })
    
    .then(response => response.json()) // Convierte la respuesta a formato JSON
    .catch(err => {
      console.error("Error al guardar en base de datos", err);
      alert("Error de red o del servidor.");
    });
});*/
