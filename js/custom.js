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

