const btnIntegraciones = document.getElementById("btnIntegraciones");
const modalIntegracion = document.getElementById("modalIntegracion");
const cerrarIntegracion = document.getElementById("cerrarIntegracion");
const contenidoPrincipal = document.getElementById("contenidoPrincipal");

btnIntegraciones.addEventListener("click", e => {
  e.preventDefault();
  modalIntegracion.style.display = "block";
  contenidoPrincipal.classList.add("blur");
});

cerrarIntegracion.addEventListener("click", () => {
  modalIntegracion.style.display = "none";
  contenidoPrincipal.classList.remove("blur");
});

window.addEventListener("click", e => {
  if (e.target === modalIntegracion) {
    modalIntegracion.style.display = "none";
    contenidoPrincipal.classList.remove("blur");
  }
});
