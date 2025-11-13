const stripe = Stripe('TU_PUBLIC_KEY_STRIPE');

document.getElementById("btnRegistro").addEventListener("click", async (e) => {
  e.preventDefault();

  const form = document.getElementById("multi-step-form");
  const formData = new FormData(form);
  const data = Object.fromEntries(formData.entries());

  // --- Validar campos obligatorios ---
  let camposVacios = false;
  const camposOpcionales = ["url_cs_emp"]; 

  for (const [key, valor] of Object.entries(data)) {
    if (typeof valor === "string" && valor.trim() === "" && !camposOpcionales.includes(key)) {
      camposVacios = true;
      break;
    }
  }

  if (camposVacios) {
    Swal.fire({
      icon: "warning",
      title: "Campos incompletos",
      text: "Por favor, complete todos los campos antes de continuar.",
      confirmButtonColor: "#3085d6",
      confirmButtonText: "Entendido"
    });
    return;
  }

  // --- Validar reCAPTCHA ---
  const captchaResponse = grecaptcha.getResponse();
  if (!captchaResponse) {
    Swal.fire({
      icon: "warning",
      title: "Captcha requerido",
      text: "Por favor completa el reCAPTCHA antes de continuar.",
      confirmButtonColor: "#3085d6",
      confirmButtonText: "Entendido"
    });
    return;
  }

  // --- Agregar token de reCAPTCHA a los datos ---
  data['g-recaptcha-response'] = captchaResponse;

  // --- Verificar plan seleccionado ---
  const planSeleccionado = data.nombre_susc;

  if (planSeleccionado === "free") {
    try {
      const response = await fetch("modelo/registro_free.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      const resultado = await response.json();

      if (resultado.status === "error") {
        Swal.fire({
          icon: "warning",
          title: "Error",
          text: resultado.message,
          confirmButtonColor: "#3085d6",
          confirmButtonText: "Entendido"
        });
      } else {
        window.location.href = "index.php";
      }
    } catch (error) {
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Ocurrió un error al registrar el plan Free: " + error.message,
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Entendido"
      });
    }
    return;
  }

  // --- Plan de pago (Stripe) ---
  try {
    const respuesta = await fetch("modelo/crear_sesion.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });

    const resultado = await respuesta.json();

    if (resultado.linkPago_susc) {
      window.location.href = resultado.linkPago_susc;
    } else {
      Swal.fire({
        icon: "warning",
        title: "Error",
        text: "Error al obtener link de pago.",
        confirmButtonColor: "#3085d6",
        confirmButtonText: "Entendido"
      });
    }
  } catch (error) {
    Swal.fire({
      icon: "warning",
      title: "Error",
      text: "Ocurrió un error en el flujo de pago. Inténtelo nuevamente.",
      confirmButtonColor: "#3085d6",
      confirmButtonText: "Entendido"
    });
  }
});
