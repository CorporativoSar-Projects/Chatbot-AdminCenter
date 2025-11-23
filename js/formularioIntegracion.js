document.addEventListener('DOMContentLoaded', () => {

  const { estadoSuscripcion, nombrePlan, sftpActivo, sftpConfig } = window.appData || {};

  const sftpLink = document.getElementById("sftpLink");
  const modalSFTP = $('#sftpModal');
  const contenido = document.getElementById("contenidoPrincipal");
  const modalSuspension = $('#modalSuspension');

  // Ocultar modal al inicio
  $('#sftpModal').modal('hide').removeClass('show').attr('aria-hidden', 'true').css('display', 'none');
  $('.modal-backdrop').remove();

  modalSFTP.on('hide.bs.modal', () => {
    if (document.activeElement) document.activeElement.blur();
  });

  // ---- ELEMENTOS ----
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

  /* ==============================================================
     🔥 ESTADO REAL DESDE BD
  ============================================================== */

  let integracionBD = sftpConfig?.tipo_integracion || "estandar";
  let activoBD = parseInt(sftpConfig?.activo) || 0;

  if (activoBD === 1) {
    if (integracionBD === "sftp") {
      activarSFTP.checked = true;
      estandarCheckbox.checked = false;
    } else {
      activarSFTP.checked = false;
      estandarCheckbox.checked = true;
    }
  } else {
    activarSFTP.checked = false;
    estandarCheckbox.checked = true;
    integracionBD = "estandar";
  }

  if (tipoIntegracion) {
    tipoIntegracion.value = integracionBD;
    tipoIntegracionHidden.value = integracionBD;
  }


  /* ==============================================================
     🔥 PLAN FREE
  ============================================================== */
  if (planFree) {

    activarSFTP.checked = false;
    activarSFTP.disabled = true;
    estandarCheckbox.checked = true;

    const opcionSFTP = tipoIntegracion.querySelector('option[value="sftp"]');
    if (opcionSFTP) opcionSFTP.remove();

    tipoIntegracion.value = "estandar";
    tipoIntegracionHidden.value = "estandar";

    const inputUrl = formEstandar?.querySelector('input[name="url_estandar"]');
    if (inputUrl) {
      inputUrl.value = window.appData.url_estandar || "";
      inputUrl.disabled = false;
      inputUrl.readOnly = false;
    }
  }


  /* ==============================================================
     🔥 PLAN FREE: evitar SFTP
  ============================================================== */
  if (planFree && formSFTP) {

    const observer = new MutationObserver(() => {
      formSFTP.querySelectorAll("input, textarea, select, button").forEach(el => {
        if (!el.disabled) el.disabled = true;
      });
    });

    observer.observe(formSFTP, {
      attributes: true,
      subtree: true,
      attributeFilter: ['disabled']
    });

    formSFTP.addEventListener("submit", (e) => {
      e.preventDefault();
      Swal.fire({
        icon: 'warning',
        title: 'Función restringida',
        text: 'El envío SFTP no está disponible en el plan Free.',
        confirmButtonColor: '#eca726'
      });
      return false;
    });
  }


  /* ==============================================================
     🔥 FUNCIÓN EXTRA → VALIDAR URL
  ============================================================== */
  function esURLValida(url) {
    const regex = /^(https?:\/\/)[\w\-]+(\.[\w\-]+)+([\/\w\-\.\?\=\&\#]*)?$/;
    return regex.test(url.trim());
  }


  /* ==============================================================
     🔥 FUNCIONES
  ============================================================== */

  function toggleInputs(disabled) {
    if (!formSFTP) return;
    formSFTP.querySelectorAll("input, button[type=submit]").forEach(input => {
      if (input.id !== "sftpCheckbox") {
        input.disabled = disabled;
      }
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


  /* ==============================================================
     🔥 INICIALIZACIÓN
  ============================================================== */

  toggleFormIntegracion();
  toggleInputs(!activarSFTP.checked);
  toggleEstandarInput();


  /* ==============================================================
     🔥 EVENTOS
  ============================================================== */

  tipoIntegracion.addEventListener("change", () => {
    toggleFormIntegracion();
  });

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


  /* ==============================================================
     🔥 ABRIR MODAL
  ============================================================== */

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

      // SFTP
      if (formSFTP && sftpConfig) {
        formSFTP.servidor.value = sftpConfig.servidor || '';
        formSFTP.puerto.value = sftpConfig.puerto || '22';
        formSFTP.usuario.value = sftpConfig.usuario || '';
        formSFTP.contrasena.value = '';
        formSFTP.rutaDestino.value = sftpConfig.rutaDestino || '';
      }

      // URL estándar
      if (formEstandar) {
        const inputUrl = formEstandar.querySelector('input[name="url_estandar"]');
        const urlEstandar =
          (window.appData.sftpConfig && window.appData.sftpConfig.url_estandar) ||
          window.appData.url_estandar || "";

        if (inputUrl) inputUrl.value = urlEstandar;
      }

      toggleInputs(!activarSFTP.checked);
      toggleFormIntegracion();
    });
  }


  /* ==============================================================
     🔥 CERRAR MODAL
  ============================================================== */

  modalSFTP.on('hidden.bs.modal', () => {
    contenido.classList.remove("blur");
    toggleInputs(!activarSFTP.checked);
  });

  const cerrarSFTP = document.getElementById("cerrarIntegracion");
  if (cerrarSFTP) cerrarSFTP.addEventListener('click', () => modalSFTP.modal('hide'));


  /* ==============================================================
     🔥 GUARDAR INTEGRACIÓN (CON VALIDACIÓN URL)
  ============================================================== */

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

      /* 🔥 VALIDAR URL SOLO EN INTEGRACIÓN ESTÁNDAR */
      if (form.id === "formIntegracionEstandar") {

        const inputUrl = form.querySelector('input[name="url_estandar"]');
        const url = inputUrl.value.trim();

        if (estandarCheckbox.checked) {

          if (!esURLValida(url)) {
            Swal.fire({
              icon: 'error',
              title: 'URL inválida',
              text: 'Por favor ingresa una URL válida. Debe empezar con http:// o https://',
              confirmButtonColor: '#eca726'
            });
            return;
          }
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

          if (form.id === "formIntegracionEstandar") {
            const inputUrlModal = form.querySelector('input[name="url_estandar"]');
            const nuevaURL = inputUrlModal?.value || "";

            if (!window.appData.sftpConfig) window.appData.sftpConfig = {};
            window.appData.sftpConfig.url_estandar = nuevaURL;

            const spanUrl = document.getElementById("urlEstandarTexto");
            if (spanUrl) spanUrl.textContent = nuevaURL;

            if (formEstandar) {
              const inputUrl = formEstandar.querySelector('input[name="url_estandar"]');
              if (inputUrl) inputUrl.value = nuevaURL;
            }
          }

          Swal.fire({
            icon: 'success',
            title: 'Integración guardada',
            text:
              formData.tipo_integracion === 'sftp'
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
