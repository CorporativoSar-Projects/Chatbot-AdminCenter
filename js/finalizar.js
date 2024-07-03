// Seleccionar el input y el contenedor del texto del chatbot
const txtSaludo = document.querySelector('#inp-saludo');
const divCopiaSaludo = document.getElementById('txt-chatbot');

 // Función para ajustar el contenido del saludo
 txtSaludo.addEventListener('keyup', () => {
    divCopiaSaludo.innerHTML = txtSaludo.value;
    // Guardar el saludo en el Local Storage
    localStorage.setItem('saludoChatbot', txtSaludo.value);
});

// Recuperar el saludo desde el Local Storage cuando la página se carga
document.addEventListener('DOMContentLoaded', () => {
    const saludoGuardado = localStorage.getItem('saludoChatbot');
    if (saludoGuardado) {
        txtSaludo.value = saludoGuardado;
        divCopiaSaludo.innerHTML = saludoGuardado;
    }
});
