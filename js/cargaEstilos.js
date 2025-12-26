(function () {
    const colores = {
        '--color-primario': localStorage.getItem('colorPrimario'),
        '--color-secundario': localStorage.getItem('colorSecundario'),
        '--color-acento': localStorage.getItem('colorAcento'),
        '--color-texto': localStorage.getItem('colorTexto'),
        '--color-respuesta-usuario': localStorage.getItem('colorRespuestaUsuario'),
    };

    for (const [key, value] of Object.entries(colores)) {
        if (value) {
            document.documentElement.style.setProperty(key, value);
        }
    }
})();
