document.addEventListener('DOMContentLoaded', () => {
  const { estadoSuscripcion, nombrePlan, sftpActivo, sftpConfig } = window.appData || {};

  const sftpLink = document.getElementById("sftpLink");
  const modalSFTP = $('#sftpModal');
  const contenido = document.getElementById("contenidoPrincipal");
  const modalSuspension = $('#modalSuspension');

  // Asegurar que el modal de integración esté oculto al cargar la página
$('#sftpModal').modal('hide').removeClass('show').attr('aria-hidden', 'true').css('display', 'none');
$('.modal-backdrop').remove();

  // Fix accesibilidad para evitar warning "Blocked aria-hidden"
  modalSFTP.on('hide.bs.modal', () => {
    if (document.activeElement) document.activeElement.blur();
  });

   // --- Elementos de los formularios  ---
  const activarSFTP = document.getElementById("sftpCheckbox");
  const estandarCheckbox = document.getElementById("estandarCheckbox");
  const formSFTP = document.getElementById("formIntegracionSFTP");
  const formEstandar = document.getElementById("formIntegracionEstandar");
  const tipoIntegracion = document.getElementById("tipoIntegracion");
  const tipoIntegracionHidden = document.getElementById("tipoIntegracionHidden");
  const btnGuardar = document.getElementById("guardarIntegracion");

  if (!activarSFTP || !estandarCheckbox) return;

  // Validaciones iniciales de plan y suscripción
  const planFree = typeof nombrePlan === 'string' && nombrePlan.trim().toLowerCase() === 'free';
  const suscripcionInactiva = estadoSuscripcion === 'paused' || estadoSuscripcion === 'canceled';

 // Activar la integración estándar por defecto
  estandarCheckbox.checked = true;
  estandarCheckbox.disabled = false; // asegurarse que no esté bloqueado

  // --- Bloquear SFTP si el plan es Free ---
if (planFree && tipoIntegracion) {
  const opcionSFTP = tipoIntegracion.querySelector('option[value="sftp"]');
  if (opcionSFTP) opcionSFTP.remove();
  tipoIntegracion.value = "estandar";
  tipoIntegracionHidden.value = "estandar";

  // Mostrar URL estándar en input
  const inputUrl = formEstandar?.querySelector('input[name="url_estandar"]');
  if (inputUrl) {
    const urlEstandar =
      (window.appData.sftpConfig && window.appData.sftpConfig.url_estandar) ||
      window.appData.url_estandar || "";
    inputUrl.value = urlEstandar;
    inputUrl.disabled = false;
    inputUrl.readOnly = false; 
  }
}

// --- Seguridad: bloquear envío de formulario SFTP si plan Free ---
if (planFree && formSFTP) {
  // Observador para mantener campos deshabilitados
   const observer = new MutationObserver(() => {
    formSFTP.querySelectorAll("input, textarea, select, button").forEach(el => {
      if (!el.disabled) {
        el.disabled = true;
      }
    });
  });

  // Observar cualquier cambio dentro del formulario
  observer.observe(formSFTP, {
    attributes: true,
    subtree: true,
    attributeFilter: ['disabled']
  });

  // Bloquear cualquier intento de envío
  formSFTP.addEventListener("submit", (e) => {
    e.preventDefault();
    e.stopImmediatePropagation();
    Swal.fire({
      icon: 'warning',
      title: 'Función restringida',
      text: 'El envío de SFTP no está disponible en el plan Free.',
      confirmButtonColor: '#eca726',
      customClass: { container: 'swal2-modal-encima' }
    });
    return false;
  });
}

// --- Funciones de utilidad ---
  function toggleInputs(disabled) {
    if (!formSFTP) return;
    formSFTP.querySelectorAll("input, button[type=submit]").forEach(input => {
      if (input.id !== "sftpCheckbox") input.disabled = disabled;
    });
  }

  function toggleFormIntegracion() {
    // Mostrar formulario según tipo seleccionado
    if (tipoIntegracion.value === "sftp") {
      formSFTP.style.display = "block";
      formEstandar.style.display = "none";
      btnGuardar.setAttribute("form", "formIntegracionSFTP");
    } else {
      formSFTP.style.display = "none";
      formEstandar.style.display = "block";
      btnGuardar.setAttribute("form", "formIntegracionEstandar");
    }
    tipoIntegracionHidden.value = tipoIntegracion.value;
  }

  function toggleEstandarInput() {
    if (!formEstandar) return;
    const inputUrl = formEstandar.querySelector('input[name="url_estandar"]');
    inputUrl.disabled = !estandarCheckbox.checked;
    if (estandarCheckbox.checked) {
      inputUrl.setAttribute("required", "required");
    } else {
      inputUrl.removeAttribute("required");
    }
  }

  // --- Exclusividad: no permitir activar ambos checkboxes ---
 function controlarExclusividad(origen) {
  if (activarSFTP.checked && estandarCheckbox.checked) {
    if (origen === 'sftp') {
      activarSFTP.checked = false; // desmarcar SFTP
    } else if (origen === 'estandar') {
      estandarCheckbox.checked = false; // desmarcar Estándar
    }

    Swal.fire({
      icon: 'info',
      title: 'Integración exclusiva',
      text: 'Solo puedes tener activa una integración a la vez (SFTP o Estándar).',
      confirmButtonColor: '#eca726',
      customClass: { container: 'swal2-modal-encima' }
    });
  }

  // Actualizar inputs según lo que quede activo
  toggleEstandarInput();
  toggleInputs(!activarSFTP.checked);
}

 // --- Inicialización según plan ---
if (planFree) {
  // Bloquear SFTP
  activarSFTP.checked = false;
  activarSFTP.disabled = true;

  // Activar solo estándar
  estandarCheckbox.checked = true;

  // Ocultar formulario SFTP
  if (formSFTP) formSFTP.style.display = 'none';
  if (formEstandar) formEstandar.style.display = 'block';

  // Eliminar opción SFTP del select
  if (tipoIntegracion) {
    const opcionSFTP = tipoIntegracion.querySelector('option[value="sftp"]');
    if (opcionSFTP) opcionSFTP.remove();
    tipoIntegracion.value = 'estandar';
    tipoIntegracionHidden.value = 'estandar';
  }


  toggleEstandarInput();

} else {
  // Plan pago: inicializar según SFTP activo
  if (sftpActivo === 1) {
    activarSFTP.checked = true;
    estandarCheckbox.checked = false;
  } else {
    activarSFTP.checked = false;
    estandarCheckbox.checked = true;
  }

  // Mostrar el formulario correspondiente
  toggleFormIntegracion();
  toggleInputs(!activarSFTP.checked);
  toggleEstandarInput();
}

  if (sftpLink) {
    sftpLink.addEventListener('click', (e) => {
      e.preventDefault();
      
      if (planFree) return;

      if (suscripcionInactiva) {
        modalSuspension.modal('show');
        return;
      }
      modalSFTP.modal({ backdrop: 'static', keyboard: false }).modal('show');
      contenido.classList.add("blur");

    // Llenar formulario SFTP
      if (formSFTP && sftpConfig) {
        formSFTP.servidor.value = sftpConfig.servidor || '';
        formSFTP.puerto.value = sftpConfig.puerto || '22';
        formSFTP.usuario.value = sftpConfig.usuario || '';
        formSFTP.contrasena.value = '';
        formSFTP.rutaDestino.value = sftpConfig.rutaDestino || '';
      }

      // Cargar valor guardado en URL estándar (desde sftpConfig o appData directo)
      if (formEstandar) {
        const inputUrl = formEstandar.querySelector('input[name="url_estandar"]');
        const urlEstandar =
          (window.appData.sftpConfig && window.appData.sftpConfig.url_estandar) ||
          window.appData.url_estandar ||
          "";

        if (inputUrl) {
          inputUrl.value = urlEstandar;
          inputUrl.setAttribute("value", urlEstandar);
        }
      }

      toggleInputs(!activarSFTP.checked);
      toggleFormIntegracion();
    });
  }

  tipoIntegracion.addEventListener("change", toggleFormIntegracion);

  
activarSFTP.addEventListener("change", () => {
  toggleInputs(!activarSFTP.checked);
  controlarExclusividad('sftp');
});

estandarCheckbox.addEventListener("change", () => {
  toggleEstandarInput();
  controlarExclusividad('estandar');
});

  modalSFTP.on('hidden.bs.modal', () => {
    contenido.classList.remove("blur");
    toggleInputs(!activarSFTP.checked);
  });

  const cerrarSFTP = document.getElementById("cerrarIntegracion");
  if (cerrarSFTP) cerrarSFTP.addEventListener('click', () => modalSFTP.modal('hide'));
//Guardar Integración
  [formSFTP, formEstandar].forEach(form => {
    if (!form) return;

    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      if (planFree && form.id === "formIntegracionSFTP") {
        modalSFTP.modal('hide');
        Swal.fire({ title: 'Función restringida', text: 'No se puede guardar SFTP en plan Free.', icon: 'warning', confirmButtonText: 'Aceptar', confirmButtonColor: '#F9BE21', customClass: { container: 'swal2-modal-encima' } });
        return;
      }
      // Preparar datos del formulario
      const formData = Object.fromEntries(new FormData(form).entries());
      if (form.id === "formIntegracionSFTP") {
        formData.activo = activarSFTP.checked ? 1 : 0;
      } else {
        formData.activo = estandarCheckbox.checked ? 1 : 0;
        if (!estandarCheckbox.checked) formData.url_estandar = '';
      }
      formData.tipo_integracion = tipoIntegracionHidden.value;

      try {
        const res = await fetch("modelo/sftp_guardar.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(formData)
        });
        const result = await res.json();

        if (result.success) {
          Swal.fire({
            icon: 'success',
            title: 'Integración guardada',
            text:
              formData.tipo_integracion === 'sftp'
                ? (activarSFTP.checked ? 'SFTP guardado correctamente.' : 'SFTP desactivado.')
                : 'Integración estándar guardada correctamente.',
            confirmButtonColor: '#eca726',
            customClass: { container: 'swal2-modal-encima' }
          });
          document.activeElement.blur();
          modalSFTP.modal('hide');
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: result.msg || "Error al guardar integración", confirmButtonColor: '#eca726', customClass: { container: 'swal2-modal-encima' } });
        }

      } catch (err) {
        Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo guardar la integración.', confirmButtonColor: '#eca726', customClass: { container: 'swal2-modal-encima' } });
      }
    });
  });
});
