// Seleccionar el input y el contenedor del texto del chatbot
const txtDespedida = document.querySelector('#inp_despedida');
const divCopiaDesp= document.getElementById('txt-chatbot-Desp');

// Cargar valor desde localStorage si existe
const despedidaGuardada = localStorage.getItem('inp_despedida');

if (despedidaGuardada) {
  txtDespedida.value = despedidaGuardada;
  divCopiaDesp.innerHTML = despedidaGuardada;
  actualizarContador();
}

// Función para ajustar el contenido del saludo
txtDespedida.addEventListener('keyup', () => {
    divCopiaDesp.innerHTML = txtDespedida.value;

    const chatbotContent = document.querySelector('.chatbot-content');
    if (chatbotContent) {
        chatbotContent.style.width = '300px';
        chatbotContent.style.height = '300px';
    }

    actualizarContador();
});

txtDespedida.addEventListener('input', () => {
  localStorage.setItem('inp_despedida', txtDespedida.value);
});

document.addEventListener('DOMContentLoaded', () => {
  const colorTexto = localStorage.getItem('colorTexto') || '#000000';
  
  
  const iconosSVG = document.querySelectorAll('.chatbot-min svg, .chatbot-close svg');

  iconosSVG.forEach(svg => {
    
    svg.style.stroke = colorTexto;  
    svg.style.fill = colorTexto;    
  });
});


document.getElementById("btnGuardarDespedida").addEventListener("click", function () {
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
});
