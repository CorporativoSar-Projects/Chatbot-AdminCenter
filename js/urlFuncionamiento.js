document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("input");
  const btnGuardarUrl = document.getElementById("btnGuardarurl");

  const colorTexto = localStorage.getItem('colorTexto') || '#000000';
  const iconosSVG = document.querySelectorAll('.chatbot-min svg, .chatbot-close svg');

  document.documentElement.style.setProperty('--color-texto-boton', colorTexto);

  iconosSVG.forEach(svg => {

    svg.style.stroke = colorTexto;
    svg.style.fill = colorTexto;
  });

  const botones = document.querySelectorAll('.chatbot-button');
  botones.forEach(btn => {
    btn.style.color = colorTexto;
  });

  // Si no se encuentra alguno, salimos para evitar errores
  if (!input || !btnGuardarUrl) return;

  // Verificar si ya hay URL registrada
  fetch("modelo/funcionamientoUrl.php?accion=verificar")
   .then(res => res.json())
    .then(data => {
      if (data.success) {
        input.value = data.url || "";

        if (data.url && data.url !== "") {
          // Ya tiene URL → bloquear edición
          input.setAttribute("readonly", true);
          btnGuardarUrl.disabled = true;
          btnGuardarUrl.classList.add("disabled");
          btnGuardarUrl.innerHTML = '<i class="fas fa-check"></i>';
        } else {
          // No tiene URL → permitir que la agregue
          input.removeAttribute("readonly");
          btnGuardarUrl.disabled = false;
          btnGuardarUrl.classList.remove("disabled");
        }
      }
    })
    .catch(error => {
      console.error("Error al verificar la URL:", error);
    });

    // --- Guardar nueva URL ---
  btnGuardarUrl.addEventListener("click", (e) => {
    e.preventDefault();
    const url = input.value.trim();

    if (!url) {
      Swal.fire({
        icon: 'warning',
        title: 'Campo vacío',
        text: 'Por favor ingresa una URL válida.',
        confirmButtonColor: '#ffb703'
      });
      return;
    }

    fetch("modelo/funcionamientoUrl.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "accion=guardar&url=" + encodeURIComponent(url)
    })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: 'La URL se ha guardado correctamente',
            confirmButtonColor: '#ffb703',
            confirmButtonText: 'Aceptar'
          });
          input.setAttribute("readonly", true);
          btnGuardarUrl.disabled = true;
          btnGuardarUrl.classList.add("disabled");
          btnGuardarUrl.innerHTML = '<i class="fas fa-check"></i>';
        } else {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message,
            confirmButtonColor: '#ffb703'
          });
        }
      })
      .catch(error => console.error("Error al guardar la URL:", error));
  });
});


