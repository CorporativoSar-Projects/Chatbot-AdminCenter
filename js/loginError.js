import {
  customAlert,
  customAlertMessage,
  customAlertClose,
  resetForm,
  cerrarModal,
  showCustomAlert,
} from "./restContra";

const idInput = document.getElementById("empresaId");
const emailInput = document.getElementById("email1");
const passwordInput = document.getElementById("password");
const buttonSesion = document.getElementById("btnSesion");

buttonSesion.addEventListener("click", function (event) {
  event.preventDefault();

  const id = idInput.value;
  const email = emailInput.value;
  const password = passwordInput.value;

  if (
    id !== "expectedId" ||
    email !== "expectedEmail" ||
    password !== "expectedPassword"
  ) {
    showCustomAlert("Usuario, correo o contraseña incorrectos.");
    customAlert.style.display = "block";
    return;
  } else if (
    id === "expectedId" &&
    email === "expectedEmail" &&
    password === "expectedPassword"
  ) {
    window.location.href = "menu.php";
  }
});

customAlertClose.addEventListener("click", function () {
  customAlert.style.display = "none";
});

window.addEventListener("click", function (event) {
  if (event.target == customAlert) {
    customAlert.style.display = "none";
  }
});
