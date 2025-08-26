$(document).ready(function() {
    const estado = window.appData.estadoSuscripcion;
    console.log("Estado de suscripción normalizado:", estado);

    if (estado === 'canceled' || estado === 'paused' || estado === 'inactivo') {
        // Bloquear sistema
        $('#modalSuspension').modal({
            backdrop: 'static',
            keyboard: false
        }).modal('show');

        $('#contenidoPrincipal :input, #contenidoPrincipal a').prop('disabled', true);

    } else if (estado === 'pendiente_cancelacion') {
        // Mostrar aviso pero no bloquear el sistema
        $('#modalAvisoCancelacion').modal('show');
    }

    // Acción del botón "Contratar Plan"
    $('#btnContratarPlan').on('click', async function() {
        const nombre_susc = $('#planSelect').val();
        if (!nombre_susc) {
            alert('Por favor selecciona un plan');
            return;
        }

        try {
            const response = await fetch('modelo/crear_suscripcion.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ nombre_susc }),
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (data.url) {
                window.location.href = data.url;
            } else if (data.error) {
                alert('Error: ' + data.error);
            }
        } catch (err) {
            console.error("Error en fetch:", err);
            alert('Ocurrió un error al crear la suscripción.');
        }
    });
});
