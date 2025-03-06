import {
  customAlert,
  customAlertMessage,
  customAlertClose,
  showCustomAlert,
} from "./restContra";

customAlertClose.addEventListener("click", function () {
  customAlert.style.display = "none";
});

window.addEventListener("click", function (event) {
  if (event.target == customAlert) {
    customAlert.style.display = "none";
  }
});

document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const errorMessage =
    urlParams.get("error") === "2"
      ? "Por favor debes iniciar sesión"
      : "Id, correo o contraseña incorrectos";
  if (urlParams.has("error")) {
    showCustomAlert(errorMessage).then(() => {
      window.location = "index.php";
    });
  }
});
