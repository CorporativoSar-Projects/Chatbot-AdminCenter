// Seleccionar el input y el contenedor del texto del chatbot
const txtDespedida = document.querySelector('#inp-depedida');
const divCopiaDesp= document.getElementById('txt-chatbot-Desp');

// Función para ajustar el contenido del saludo
txtDespedida.addEventListener('keyup', () => {
    divCopiaDesp.innerHTML = txtDespedida.value;
    
    // Aplicar el mismo tamaño fijo a .chatbot-content
    const chatbotContent = document.querySelector('.chatbot-content');
    chatbotContent.style.width = '300px';
    chatbotContent.style.height = '300px';
    

    
});