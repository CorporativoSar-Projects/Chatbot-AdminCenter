/* Se importan la ventana modal con un mensaje personalizado */
import showCustomAlert from "./restContra";

/* Función que permite leer una url con el parametro de error y muestra el respectivo mensaje */

document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  const errorMessage =
    urlParams.get("error") === "2"
      ? "Por favor debes iniciar sesión"
      : "Id, correo o contraseña incorrectos";
  if (urlParams.has("error")) {
    /* Si hay algun error el usurrio sera redirigido al formulario de login */
    showCustomAlert(errorMessage).then(() => {
      window.location.href = "index.php";
    });
  }
});
