const stripe = Stripe('pk_test_...');//public key de stripe

document.getElementById("btnRegistro").addEventListener("click", async (e) => {
  e.preventDefault();

  const form = document.getElementById("multi-step-form");
  const formData = new FormData(form);
  const data = Object.fromEntries(formData.entries());

  const respuesta = await fetch("modelo/crear_sesion.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(data),
  });

  const resultado = await respuesta.json();

  if (resultado.linkPago_susc
  ) {
    // Redirigir directamente al link de pago
    window.location.href = resultado.linkPago_susc;
  } else {
    alert("Error al obtener link de pago.");
  }
});
