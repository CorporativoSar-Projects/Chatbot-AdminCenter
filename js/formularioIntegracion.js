document.addEventListener('DOMContentLoaded', () => {
  const { estadoSuscripcion, nombrePlan, sftpActivo, sftpConfig } = window.appData || {};

  const sftpLink = document.getElementById("sftpLink");
  const modalSFTP = $('#sftpModal'); 
  const contenido = document.getElementById("contenidoPrincipal");
  const modalSuspension = $('#modalSuspension');
  const activarSFTP = document.getElementById("sftpCheckbox");
  const formSFTP = document.getElementById("formIntegracionSFTP");

  if (!activarSFTP) return;

  const planFree = typeof nombrePlan === 'string' && nombrePlan.trim().toLowerCase() === 'free';
  const suscripcionInactiva = estadoSuscripcion === 'paused' || estadoSuscripcion === 'canceled';

  // Función para habilitar/deshabilitar inputs del formulario
  function toggleInputs(disabled) {
    if (!formSFTP) return;
    const inputs = formSFTP.querySelectorAll("input, button[type=submit]");
    inputs.forEach(input => {
      if (input.id !== "sftpCheckbox") { // nunca deshabilitar el checkbox
        input.disabled = disabled;
      }
    });
  }

  // Inicializar checkbox y bloquear inputs si está desactivado
  activarSFTP.checked = sftpActivo === 1 && !planFree;
  activarSFTP.disabled = planFree;
  toggleInputs(!activarSFTP.checked);
  window.appData.sftpActivo = sftpActivo;

  // Abrir modal desde enlace
  if (sftpLink) {
    sftpLink.addEventListener('click', (e) => {
      e.preventDefault();

      if (planFree) {
        Swal.fire({
          title: 'Función restringida',
          text: 'Esta función está disponible solo con un plan de pago.',
          icon: 'warning',
          confirmButtonText: 'Aceptar',
          confirmButtonColor: '#F9BE21',
            didOpen: () => {
            const swalContainer = document.querySelector('.swal2-container');
            if (swalContainer) {
              swalContainer.style.zIndex = 20000; // más alto que Bootstrap modal
            }
          }
        });
        return;
      }
      if (suscripcionInactiva) {
        modalSuspension.modal('show');
        return;
      }

      modalSFTP.modal({ backdrop: 'static', keyboard: false }).modal('show');
      contenido.classList.add("blur");

      // Prellenar formulario si hay datos (aunque activo = 0)
      if (formSFTP && sftpConfig) {
        formSFTP.servidor.value = sftpConfig.servidor || '';
        formSFTP.puerto.value = sftpConfig.puerto || '22';
        formSFTP.usuario.value = sftpConfig.usuario || '';
        formSFTP.contrasena.value = ''; // nunca mostrar hash
        formSFTP.rutaDestino.value = sftpConfig.rutaDestino || '';
      }
      toggleInputs(!activarSFTP.checked);
    });
  }

  // 🔹 Checkbox solo habilita/deshabilita inputs en pantalla
  activarSFTP.addEventListener("change", () => {
    toggleInputs(!activarSFTP.checked);
  });

  // Quitar blur al cerrar modal
  modalSFTP.on('hidden.bs.modal', () => {
    contenido.classList.remove("blur");
    toggleInputs(!activarSFTP.checked);
  });

  // Botón cerrar dentro del modal
  const cerrarSFTP = document.getElementById("cerrarIntegracion");
  if (cerrarSFTP) cerrarSFTP.addEventListener('click', () => modalSFTP.modal('hide'));

  // Guardar formulario SFTP
  if (formSFTP) {
    formSFTP.addEventListener("submit", async (e) => {
      e.preventDefault();
      if (planFree) {
         // Cerrar modal primero
        modalSFTP.modal('hide');
         Swal.fire({
          title: 'Función restringida',
          text: 'No se puede guardar SFTP en plan Free.',
          icon: 'warning',
          confirmButtonText: 'Aceptar',
          confirmButtonColor: '#F9BE21',
            didOpen: () => {
            const swalContainer = document.querySelector('.swal2-container');
            if (swalContainer) {
              swalContainer.style.zIndex = 20000; // más alto que Bootstrap modal
            }
          }
        });
        return;
      }

      const formData = Object.fromEntries(new FormData(formSFTP).entries());
      // ✅ tomar valor real del checkbox
      formData.activo = activarSFTP.checked ? 1 : 0;

      try {
        const res = await fetch("modelo/sftp_guardar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(formData)
        });
        const result = await res.json();

        if (result.success) {
          if (formData.activo === 1) {
            Swal.fire({
              icon: 'success',
              title: 'Integración guardada',
              text: 'La configuración de SFTP se ha guardado correctamente.',
              confirmButtonColor: '#eca726'
            });
          } else {
           Swal.fire({
            icon: 'info',
            title: 'Integración desactivada',
            text: 'La integración con SFTP ha sido deshabilitada.',
            confirmButtonColor: '#eca726'
          });

          }

          modalSFTP.modal('hide');
          window.appData.sftpActivo = formData.activo;
          activarSFTP.checked = formData.activo === 1;
          toggleInputs(!activarSFTP.checked);
        } else {
          alert(result.msg || "Error al guardar integración");
          toggleInputs(!activarSFTP.checked);
        }

      } catch (err) {
        alert("Error de conexión al guardar integración");
        toggleInputs(!activarSFTP.checked);
      }
    });
  }
});
