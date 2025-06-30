  document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("input");
  const btnGuardarUrl = document.getElementById("btnGuardarurl");

  // Si no se encuentra alguno, salimos para evitar errores
  if (!input || !btnGuardarUrl) return;

  // Verificar si ya hay URL registrada
  fetch("modelo/funcionamientoUrl.php?accion=verificar")
    .then(res => res.json())
    .then(data => {
      input.value = data.url || "";

      if (!data.editable) {
        input.setAttribute("readonly", true);
        btnGuardarUrl.disabled = true;
        btnGuardarUrl.classList.add("disabled");
      } else {
        input.removeAttribute("readonly");
        btnGuardarUrl.disabled = false;
        btnGuardarUrl.classList.remove("disabled");
      }
    })
    .catch(error => {
      console.error("Error al verificar la URL:", error);
    });

  // Guardar nueva URL al hacer clic
  btnGuardarUrl.addEventListener("click", function (e) {
    e.preventDefault();
    const url = input.value.trim();

    if (!url) {
      alert("Por favor ingresa una URL.");
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
          alert("URL guardada correctamente.");
          input.setAttribute("readonly", true);
          btnGuardarUrl.disabled = true;
          btnGuardarUrl.classList.add("disabled");
          btnGuardarUrl.innerHTML = '<i class="fas fa-check"></i>';
        } else {
          alert("Error: " + data.message);
        }
      })
      .catch(error => {
        console.error("Error al guardar la URL:", error);
      });
  });
});

