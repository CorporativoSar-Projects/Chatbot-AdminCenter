document.addEventListener('DOMContentLoaded', () => {
  const { estadoSuscripcion, nombrePlan } = window.appData || {};

  const checkbox = document.getElementById("sftpCheckbox");
  const modalSFTP = document.getElementById("sftpModal");
  const cerrarSFTP = document.getElementById("cerrarIntegracion");
  const contenido = document.getElementById("contenidoPrincipal");
  const modalSuspension = document.getElementById('modalSuspension');

  // Normalizamos valores
  const planFree = typeof nombrePlan === 'string' && nombrePlan.trim().toLowerCase() === 'free';
  const suscripcionInactiva = estadoSuscripcion === 'paused' || estadoSuscripcion === 'canceled';

  // Función para mostrar modal de suspensión
  const mostrarModalSuspension = () => {
    if (modalSuspension) modalSuspension.style.display = 'flex';
  };

  // BLOQUEAR checkbox si plan Free
  if (checkbox) {
    if (planFree || suscripcionInactiva) {
      checkbox.disabled = true;
      checkbox.checked = false;
      checkbox.title = planFree
        ? 'Esta función está disponible solo con un plan de pago.'
        : 'Tu suscripción está pausada o cancelada.';

      // Mostrar alerta/modal al intentar hacer clic
      checkbox.addEventListener('mousedown', (e) => {
        e.preventDefault();
        if (planFree) alert('Esta función está disponible solo con un plan de pago.');
        else if (suscripcionInactiva) mostrarModalSuspension();
      });

      // Mostrar modal de suspensión automáticamente si corresponde
      if (suscripcionInactiva) mostrarModalSuspension();
    } else {
      // abrir modal SFTP
      checkbox.addEventListener("change", () => {
        if (checkbox.checked) {
          modalSFTP.style.display = "block";
          contenido.classList.add("blur");
        } else {
          modalSFTP.style.display = "none";
          contenido.classList.remove("blur");
        }
      });
    }
  }

  // Cerrar modal SFTP
  if (cerrarSFTP) {
    cerrarSFTP.addEventListener('click', () => {
      modalSFTP.style.display = "none";
      contenido.classList.remove("blur");
      if (checkbox) checkbox.checked = false;
    });
  }

  // Cerrar modal SFTP al hacer clic fuera
  window.addEventListener("click", (e) => {
    if (e.target === modalSFTP) {
      modalSFTP.style.display = "none";
      contenido.classList.remove("blur");
      if (checkbox) checkbox.checked = false;
    }
  });

  // Cerrar modal de suspensión si agregas un botón de cierre
  const cerrarModalSusp = document.getElementById('cerrarModalSuspension');
  if (cerrarModalSusp) {
    cerrarModalSusp.addEventListener('click', () => {
      if (modalSuspension) modalSuspension.style.display = 'none';
    });
  }
});
