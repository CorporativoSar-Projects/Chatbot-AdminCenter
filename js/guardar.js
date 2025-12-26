document.addEventListener("DOMContentLoaded", function () {
    const btnFinal = document.getElementById("btnGuardarFinalizar");
    const btnCerrar = document.getElementById("btnCerrar");

    if (btnFinal) {
        btnFinal.addEventListener("click", function (event) {
            event.preventDefault();

            fetch("modelo/guardar_datos.php", {
                method: "POST"
            })
            .then(response => response.json());
        });
    }

    // limpia los campos al salir de la configuración
    if (btnCerrar) {
        btnCerrar.addEventListener("click", () => {
            const tempActivo = localStorage.getItem('ixah_tour_activo');
            const tempVisto = localStorage.getItem('ixah_tour_visto');

            localStorage.clear();

            if (tempActivo !== null) localStorage.setItem('ixah_tour_activo', tempActivo);
            if (tempVisto !== null) localStorage.setItem('ixah_tour_visto', tempVisto);
        });
    }
});
