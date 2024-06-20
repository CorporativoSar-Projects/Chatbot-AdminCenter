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

document.getElementById('archivoLogotipo').addEventListener('change', function() {
    var nombreArchivo = this.files[0] ? this.files[0].name : 'Ningún archivo seleccionado';
    document.getElementById('nombreArchivo').textContent = nombreArchivo;
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



function agregarTema() {
    const listaTemas = document.getElementById('listaTemas');
    const temasActuales = listaTemas.getElementsByTagName('li');

    
    if (temasActuales.length >= 5) {
        // alert('Solo puedes agregar hasta 5 temas de conversación.');
        return;
    }

    // Crear el nuevo elemento de tema
    const nuevoTema = document.createElement('li');
    nuevoTema.className = 'tema-item';

    // Crear el input para el nuevo tema
    const nuevoInput = document.createElement('input');
    nuevoInput.type = 'text';
    nuevoInput.name = 'inp-conversa';
    nuevoInput.placeholder = 'Nuevo tema de conversación';
    nuevoInput.className = 'inp-conversa';
    nuevoInput.required = true;

 
    const botonBorrar = document.createElement('button');
    botonBorrar.className = 'btn-borrar';
    botonBorrar.onclick = function() {
        eliminarTema(botonBorrar);
    };

 
    const imagenBorrar = document.createElement('img');
    imagenBorrar.src = 'img/trash.png';
    imagenBorrar.width = 20;
    imagenBorrar.alt = 'Delete Topic';

    botonBorrar.appendChild(imagenBorrar);

  
    nuevoTema.appendChild(nuevoInput);
    nuevoTema.appendChild(botonBorrar);

 
    listaTemas.appendChild(nuevoTema);
}


function eliminarTema(elemento) {
    const temaItem = elemento.parentNode;
    temaItem.remove();
}
