// Seleccionar el input y el contenedor del texto del chatbot
const txtSaludo = document.querySelector('#inp-saludo');
const divCopiaSaludo = document.getElementById('txt-chatbot');

// Función para ajustar el contenido del saludo
txtSaludo.addEventListener('keyup', () => {
    divCopiaSaludo.innerHTML = txtSaludo.value;
    
    // Aplicar el mismo tamaño fijo a .chatbot-content
    const chatbotContent = document.querySelector('.chatbot-content');
    chatbotContent.style.width = '300px';
    chatbotContent.style.height = '300px';
    

    
});



// Actualizar botones del chatbot al escribir en los inputs
function actualizarBotonChatbot(input, boton) {
    boton.textContent = input.value || 'Nuevo tema'; // Usar el valor del input o texto por defecto
}

// Función para crear y agregar un nuevo botón al chatbot
function crearBotonChatbot(input) {
    const chatbotButtonsContainer = document.getElementById('chatbot-buttons');
    const boton = document.createElement('button');
    boton.className = 'chatbot-button';
    actualizarBotonChatbot(input, boton); // Establecer texto inicial
    chatbotButtonsContainer.appendChild(boton);
    
    // Actualizar el botón en tiempo real
    input.addEventListener('input', () => actualizarBotonChatbot(input, boton));
}

// Inicializar los botones del chatbot para los inputs existentes
document.querySelectorAll('.inp-conversa').forEach(input => {
    crearBotonChatbot(input);
});

function agregarTema() {
    const listaTemas = document.getElementById('listaTemas');
    const temasActuales = listaTemas.querySelectorAll('.tema-item').length;
    const maxTemas = 5;
    const errorMensaje = document.getElementById('errorMensaje');

    if (temasActuales >= maxTemas) {
        // Mostrar mensaje de error
        errorMensaje.style.display = 'block';
        return; // Salir de la función si ya hay 5 temas
    }

    // Ocultar mensaje de error si se está agregando un nuevo tema
    errorMensaje.style.display = 'none';

    // Crear nuevo elemento li
    const nuevoTema = document.createElement('li');
    nuevoTema.className = 'tema-item';

    // Crear el input y botón de borrar
    const nuevoInput = document.createElement('input');
    nuevoInput.type = 'text';
    nuevoInput.name = 'inp-conversa';
    nuevoInput.className = 'inp-conversa';
    nuevoInput.placeholder = 'Nuevo tema de conversación';
    nuevoInput.required = true;

    const btnBorrar = document.createElement('button');
    btnBorrar.className = 'btn-borrar';
    btnBorrar.innerHTML = '<img src="img/trash.png" width="20" alt="Delete Topic">';
    btnBorrar.onclick = function () {
        eliminarTema(btnBorrar);
    };

    // Añadir el input y el botón de borrar al nuevo tema
    nuevoTema.appendChild(nuevoInput);
    nuevoTema.appendChild(btnBorrar);

    // Añadir el nuevo tema a la lista
    listaTemas.appendChild(nuevoTema);

    // Crear un nuevo botón del chatbot
    crearBotonChatbot(nuevoInput);
}

// Función para eliminar un tema de conversación
function eliminarTema(btn) {
    const temaItem = btn.parentElement;
    temaItem.remove();

    // Actualizar los botones del chatbot
    const chatbotButtonsContainer = document.getElementById('chatbot-buttons');
    chatbotButtonsContainer.innerHTML = ''; // Limpiar los botones actuales
    document.querySelectorAll('.inp-conversa').forEach(input => {
        crearBotonChatbot(input);
    });

    // Ocultar mensaje de error si se elimina un tema y el número de temas es menor a 5
    const temasActuales = document.querySelectorAll('.tema-item').length;
    if (temasActuales < 5) {
        document.getElementById('errorMensaje').style.display = 'none';
    }
}
