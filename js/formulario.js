  const checkbox = document.getElementById("sftpCheckbox");
  const modal = document.getElementById("sftpModal");
  const cerrar = document.getElementById("cerrarIntegracion");
  const contenido = document.getElementById("contenidoPrincipal");

  checkbox.addEventListener("change", () => {
    if (checkbox.checked) {
      modal.style.display = "block";
      contenido.classList.add("blur");
    } else {
      modal.style.display = "none";
      contenido.classList.remove("blur");
    }
  });

  cerrar.addEventListener("click", () => {
    modal.style.display = "none";
    contenido.classList.remove("blur");
    checkbox.checked = false;
  });

  window.addEventListener("click", (e) => {
    if (e.target === modal) {
      modal.style.display = "none";
      contenido.classList.remove("blur");
      checkbox.checked = false;
    }
  });
