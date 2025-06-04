// Seleccionar el input y el contenedor del texto del chatbot
const txtDespedida = document.querySelector('#inp_despedida');
const divCopiaDesp= document.getElementById('txt-chatbot-Desp');

// Cargar valor desde localStorage si existe
const despedidaGuardada = localStorage.getItem('inp_despedida');

if (despedidaGuardada) {
  txtDespedida.value = despedidaGuardada;
  divCopiaDesp.innerHTML = despedidaGuardada;
}

// Función para ajustar el contenido del saludo
txtDespedida.addEventListener('keyup', () => {
    divCopiaDesp.innerHTML = txtDespedida.value;
    
    // Aplicar el mismo tamaño fijo a .chatbot-content
    const chatbotContent = document.querySelector('.chatbot-content');
    chatbotContent.style.width = '300px';
    chatbotContent.style.height = '300px';
    
});

txtDespedida.addEventListener('input', () => {
  localStorage.setItem('inp_despedida', txtDespedida.value);
});

//Objeto con el que los datos se van a guardar
document.getElementById("btnGuardarDespedida").addEventListener("click", function () {
  const datos = {
    "inp_despedida": document.getElementById('inp_despedida').value
  };

   // Envía los datos al archivo PHP mediante fetch
  fetch("modelo/guardar_chatbot.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      seccion: "despedida", // Seccion donde se guardaran los datos
      datos: datos
    })
  })
    .then(res => res.json())
    .catch(err => {
      console.error("Error al guardar en sesión", err);
    });
});
