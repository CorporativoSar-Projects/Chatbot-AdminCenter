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

function actualizarColoresBurbuja(){
    const colorPrimarioBurbuja = document.getElementById('colorPrimarioBurbuja').value;
    document.documentElement.style.setProperty('--colorPrimarioBurbuja', colorPrimarioBurbuja);
}

const txtBurbuja = document.querySelector('#inp-burbuja');
const divCopiaBurb = document.getElementById('chatTextBurb');

txtBurbuja.addEventListener('keyup', () => {
    divCopiaBurb.innerHTML = txtBurbuja.value;
});