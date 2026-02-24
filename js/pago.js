const stripe = Stripe('...');//public key de stripe

document.getElementById("btnRegistro").addEventListener("click", async (e) => {
  e.preventDefault();

  const form = document.getElementById("multi-step-form");
  const formData = new FormData(form);
  const data = Object.fromEntries(formData.entries());

  // --- Validar que todos los campos estén llenos ---
  let camposVacios = false;

  // Define aquí los campos opcionales
  const camposOpcionales = ["url_cs_emp"]; // nombre del campo URL opcional

  for (const [key, valor] of Object.entries(data)) {
    if (typeof valor === "string" && valor.trim() === "") {
      // Solo marcar error si NO es un campo opcional
      if (!camposOpcionales.includes(key)) {
        camposVacios = true;
        break;
      }
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

  // --- Verificar plan seleccionado ---
  const planSeleccionado = data.nombre_susc;

  if (planSeleccionado === "free") {
    // --- Plan Free: enviar al backend directamente ---
    const formDataFinal = new FormData();
    formDataFinal.append("registro", JSON.stringify(data));

    try {
      const response = await fetch("modelo/registro_free.php", {
        method: "POST",
        body: formDataFinal,
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
        // Registro exitoso
        window.location.href = "index.php";
      }
    } catch (error) {
      alert("Ocurrió un error al registrar el plan Free: " + error.message);
    }

    return; // Detiene la ejecución, no se va a Stripe
  }
  // --- Plan de pago: flujo normal de Stripe ---
  try {
    const respuesta = await fetch("modelo/crear_sesion.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(data),
    });

    const resultado = await respuesta.json();

    if (resultado.linkPago_susc) {
      // Redirigir directamente al link de pago
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