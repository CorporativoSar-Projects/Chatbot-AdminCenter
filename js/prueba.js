window.addEventListener('resize', function() {
    const width = window.innerWidth;
    const btnCerrar = document.querySelector('.btnCerrar');
    const btnGuardarS = document.querySelector('.btnGuardarS');
    const btnContinuar = document.querySelector('.btnContinuar');
    
    if (width <= 480) {
        btnCerrar.innerHTML = '<i class="fas fa-times"></i>';
        btnGuardarS.innerHTML = '<i class="fas fa-save"></i>';
        btnContinuar.innerHTML = '<i class="fas fa-arrow-right"></i>';
        btnRegresar.innerHTML = '<i class="fas fa-arrow-left"></i>';
    } else {
        btnRegresar.innerHTML = 'Regresar';
        btnContinuar.innerHTML = 'Continuar';
        btnCerrar.innerHTML = 'Cerrar';
        btnGuardarS.innerHTML = 'Guardar y salir';
        
    }
});

window.dispatchEvent(new Event('resize'));