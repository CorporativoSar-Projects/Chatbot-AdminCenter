document.addEventListener("DOMContentLoaded", function() {
    const inputs = [
        { id: 'colorPrimarioBurbuja', defaultValue: '#e39842' },
        { id: 'colorTextoBurbuja', defaultValue: '#000000' }
    ];

    inputs.forEach(inputData => {
        const inputElement = document.getElementById(inputData.id);
        const muestraElement = document.getElementById(`muestra${capitalize(inputData.id)}`);

        muestraElement.style.backgroundColor = inputElement.value;

        inputElement.addEventListener("input", function() {
            const color = inputElement.value;
            if (isValidHex(color)) {
                muestraElement.style.backgroundColor = color;
                actualizarColores();
            }
        });
    });
});

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

function isValidHex(color) {
    const hexRegex = /^#([0-9A-F]{3}){1,2}$/i;
    return hexRegex.test(color);
}

function actualizarColores() {
    const colors = {
        '--colorPrimarioBurbuja': document.getElementById('colorPrimarioBurbuja').value,
        '--colorTextoBurbuja': document.getElementById('colorTextoBurbuja').value,
    };

    for (const [key, value] of Object.entries(colors)) {
        if (isValidHex(value)) {
            document.documentElement.style.setProperty(key, value);
        }
    }
}

const txtBurbuja = document.querySelector('#inp-burbuja');
const divCopiaBurb = document.getElementById('chatTextBurb');

txtBurbuja.addEventListener('keyup', () => {
    divCopiaBurb.innerHTML = txtBurbuja.value;
});


