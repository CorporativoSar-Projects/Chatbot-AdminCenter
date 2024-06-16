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

    // Aplicar los colores al chatbot usando variables CSS
    document.documentElement.style.setProperty('--color-primario', colorPrimario);
    document.documentElement.style.setProperty('--color-secundario', colorSecundario);
    document.documentElement.style.setProperty('--color-acento', colorAcento);
    document.documentElement.style.setProperty('--color-texto', colorTexto);

    // Ajustar colores específicos si es necesario
    document.documentElement.style.setProperty('--color-texto-header', '#ffffff'); // Texto del header siempre blanco
    document.documentElement.style.setProperty('--color-texto-boton', colorTexto); // Texto de los botones
    document.documentElement.style.setProperty('--color-texto-hover', '#ffffff'); // Texto de los botones al pasar el ratón
}