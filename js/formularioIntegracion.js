document.addEventListener('DOMContentLoaded', () => {

  const { estadoSuscripcion, nombrePlan, sftpConfig } = window.appData || {};

  const sftpLink = document.getElementById("sftpLink");
  const modalSFTP = $('#sftpModal');
  const contenido = document.getElementById("contenidoPrincipal");
  const modalSuspension = $('#modalSuspension');

  const activarSFTP = document.getElementById("sftpCheckbox");
  const estandarCheckbox = document.getElementById("estandarCheckbox");
  const formSFTP = document.getElementById("formIntegracionSFTP");
  const formEstandar = document.getElementById("formIntegracionEstandar");
  const tipoIntegracion = document.getElementById("tipoIntegracion");
  const tipoIntegracionHidden = document.getElementById("tipoIntegracionHidden");
  const btnGuardar = document.getElementById("guardarIntegracion");

  

  if (!activarSFTP || !estandarCheckbox) return;

  const planFree = typeof nombrePlan === 'string' && nombrePlan.trim().toLowerCase() === 'free';
  const suscripcionInactiva = estadoSuscripcion === 'paused' || estadoSuscripcion === 'canceled';


  // -----------------------------------------
  // Funciones de utilidad
  // -----------------------------------------
  function esURLValida(url) {
    try {
      // Si no empieza con http:// o https://, se lo agregamos temporalmente
      if (!/^https?:\/\//i.test(url)) url = 'http://' + url;
      new URL(url); // Intentamos crear un objeto URL
      return true;
    } catch (err) {
      return false;
    }
  }


  function toggleInputs(disabled) {
    if (!formSFTP) return;
    formSFTP.querySelectorAll("input, button[type=submit]").forEach(input => {
      if (input.id !== "sftpCheckbox") input.disabled = disabled;
    });
  }

  function toggleFormIntegracion() {
    if (tipoIntegracion.value === "sftp") {
      formSFTP.style.display = "block";
      formEstandar.style.display = "none";
      btnGuardar.setAttribute("form", "formIntegracionSFTP");
    } else {
      formSFTP.style.display = "none";
      formEstandar.style.display = "block";
      btnGuardar.setAttribute("form", "formIntegracionEstandar");

      // Actualizar el input de URL estándar inmediatamente
      if (formEstandar) {
        const inputUrl = formEstandar.querySelector('input[name="url_estandar"]');
        if (inputUrl) inputUrl.value = sftpConfig?.url_estandar || window.appData.url_estandar || "";
      }
    }
    tipoIntegracionHidden.value = tipoIntegracion.value;
  }

  function toggleEstandarInput() {
    if (!formEstandar) return;
    const inputUrl = formEstandar.querySelector('input[name="url_estandar"]');
    inputUrl.disabled = !estandarCheckbox.checked;
    if (estandarCheckbox.checked) inputUrl.setAttribute("required", "required");
    else inputUrl.removeAttribute("required");
  }

  function controlarExclusividad(origen) {
    if (activarSFTP.checked && estandarCheckbox.checked) {
      if (origen === 'sftp') activarSFTP.checked = false;
      else estandarCheckbox.checked = false;

      Swal.fire({
        icon: 'info',
        title: 'Integración exclusiva',
        text: 'Solo puedes tener activa una integración a la vez.',
        confirmButtonColor: '#eca726'
      });
    }
    toggleEstandarInput();
    toggleInputs(!activarSFTP.checked);
  }

  // -----------------------------------------
  // Inicialización según BD y plan
  // -----------------------------------------
  (function inicializarIntegracion() {
    const activo = parseInt(sftpConfig?.activo) === 1;
    const tipo = sftpConfig?.tipo_integracion || "estandar";

    activarSFTP.checked = activo && tipo === "sftp";
    estandarCheckbox.checked = !(activo && tipo === "sftp");


    if (tipoIntegracion) tipoIntegracion.value = tipo;
    if (tipoIntegracionHidden) tipoIntegracionHidden.value = tipo;

    toggleFormIntegracion();
    toggleInputs(!activarSFTP.checked);
    toggleEstandarInput();

    if (planFree) {
  //  Forzar solo integración estándar
  activarSFTP.checked = false;
  activarSFTP.disabled = true;

  estandarCheckbox.checked = true;
  estandarCheckbox.disabled = false;

  tipoIntegracion.value = "estandar";
  tipoIntegracionHidden.value = "estandar";

  //  Ocultar todo SFTP
  if (formSFTP) formSFTP.style.display = "none";
  if (activarSFTP) activarSFTP.parentElement.style.display = "none";

  // Ocultar opción SFTP del select
  const optSftp = tipoIntegracion.querySelector('option[value="sftp"]');
  if (optSftp) optSftp.style.display = "none";

  //  Bloquear select para evitar cambios
  tipoIntegracion.disabled = true;

  //  Mostrar solo estándar
  if (formEstandar) {
    formEstandar.style.display = "block";
    const inputUrl = formEstandar.querySelector('input[name="url_estandar"]');
    if (inputUrl) {
      inputUrl.value = window.appData?.url_estandar || "";
      inputUrl.disabled = false;
      inputUrl.readOnly = false;
    }
  }

  return; // Detener aquí. No ejecutar más lógica.
}
  })();

  // -----------------------------------------
  // Eventos de checkboxes y selects
  // -----------------------------------------
  tipoIntegracion.addEventListener("change", toggleFormIntegracion);

  activarSFTP.addEventListener("change", () => {
    tipoIntegracion.value = "sftp";
    tipoIntegracionHidden.value = "sftp";
    controlarExclusividad('sftp');
    toggleFormIntegracion();
  });

  estandarCheckbox.addEventListener("change", () => {
    tipoIntegracion.value = "estandar";
    tipoIntegracionHidden.value = "estandar";
    controlarExclusividad('estandar');
    toggleFormIntegracion();
  });


  // -----------------------------------------
  // Modal SFTP
  // -----------------------------------------
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

      if (formSFTP && sftpConfig) {
        formSFTP.servidor.value = sftpConfig.servidor || '';
        formSFTP.puerto.value = sftpConfig.puerto || '22';
        formSFTP.usuario.value = sftpConfig.usuario || '';
        formSFTP.contrasena.value = '';
        formSFTP.rutaDestino.value = sftpConfig.rutaDestino || '';
      }

      if (formEstandar) {
        const inputUrl = formEstandar.querySelector('input[name="url_estandar"]');
        if (inputUrl) inputUrl.value = sftpConfig?.url_estandar || window.appData.url_estandar || "";
      }

      toggleInputs(!activarSFTP.checked);
      toggleFormIntegracion();
    });
  }


  modalSFTP.on('hidden.bs.modal', () => {
    contenido.classList.remove("blur");
    toggleInputs(!activarSFTP.checked);
  });

  const cerrarSFTP = document.getElementById("cerrarIntegracion");
  if (cerrarSFTP) cerrarSFTP.addEventListener('click', () => modalSFTP.modal('hide'));


  // -----------------------------------------
  // Guardar integración
  // -----------------------------------------
  [formSFTP, formEstandar].forEach(form => {
    if (!form) return;

    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      if (planFree && form.id === "formIntegracionSFTP") {
        modalSFTP.modal('hide');
        Swal.fire({
          title: 'Función restringida',
          text: 'No se puede guardar SFTP en plan Free.',
          icon: 'warning',
          confirmButtonColor: '#F9BE21'
        });
        return;
      }

      const formData = Object.fromEntries(new FormData(form).entries());
      if (form.id === "formIntegracionEstandar") {
        const url = form.querySelector('input[name="url_estandar"]').value.trim();
        if (estandarCheckbox.checked && !esURLValida(url)) {
          Swal.fire({
            icon: 'error',
            title: 'URL inválida',
            text: 'Por favor ingresa una URL válida. Debe empezar con http:// o https://',
            confirmButtonColor: '#eca726'
          });
          return;
        }
        formData.activo = estandarCheckbox.checked ? 1 : 0;
        if (!estandarCheckbox.checked) formData.url_estandar = '';
      } else {
        formData.activo = activarSFTP.checked ? 1 : 0;
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
          // Actualizar window.appData con los valores guardados
          if (formData.tipo_integracion === 'estandar') {
            window.appData.sftpConfig.url_estandar = formData.url_estandar;
            sftpConfig.url_estandar = formData.url_estandar; // también para el modal actual
          } else if (formData.tipo_integracion === 'sftp') {
            // actualizar sftpConfig con los datos SFTP si quieres reflejar cambios
            sftpConfig.servidor = formData.servidor || '';
            sftpConfig.puerto = formData.puerto || '22';
            sftpConfig.usuario = formData.usuario || '';
            sftpConfig.rutaDestino = formData.rutaDestino || '';
          }
          Swal.fire({
            icon: 'success',
            title: 'Integración guardada',
            text: formData.tipo_integracion === 'sftp'
              ? (activarSFTP.checked ? 'SFTP guardado correctamente.' : 'SFTP desactivado.')
              : 'Integración estándar guardada correctamente.',
            confirmButtonColor: '#eca726'
          });
          modalSFTP.modal('hide');
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: result.msg || "Error al guardar integración",
            confirmButtonColor: '#eca726'
          });
        }
      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'Error de conexión',
          text: 'No se pudo guardar la integración.',
          confirmButtonColor: '#eca726'
        });
      }
    });
  });

});