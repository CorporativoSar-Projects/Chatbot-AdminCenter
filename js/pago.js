const stripe = Stripe('...');//public key de stripe

document.getElementById("btnRegistro").addEventListener("click", async (e) => {
  e.preventDefault();

  const form = document.getElementById("multi-step-form");
  const formData = new FormData(form);
  const data = Object.fromEntries(formData.entries());

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

      const texto = await response.text();
      if (texto.includes("exitoso")) {
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
      alert("Error al obtener link de pago.");
    }
  } catch (error) {
    alert("Error en el flujo de pago: " + error.message);
  }
});