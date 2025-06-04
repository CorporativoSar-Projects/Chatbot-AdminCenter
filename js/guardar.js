document.addEventListener("DOMContentLoaded", function () {
    const btnFinal = document.getElementById("btnGuardarFinalizar");

    btnFinal.addEventListener("click", function (event) {
        event.preventDefault();

        fetch("modelo/guardar_datos.php", {
            method: "POST"
        })
        .then(response => response.json())
    });
});

//limpia los campos al salir de la configuración
document.getElementById('btnCerrar').addEventListener('click', () => {
  localStorage.clear(); 
  
});