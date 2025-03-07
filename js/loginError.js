import { customAlert, showCustomAlert } from "./restContra";

document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const errorMessage =
    urlParams.get("error") === "2"
      ? "Por favor debes iniciar sesión"
      : "Id, correo o contraseña incorrectos";
  if (urlParams.has("error")) {
    showCustomAlert(errorMessage).then(() => {
      window.location.href = "index.php";
    });
  }
});
