const customAlert = document.getElementById("custom-alert");
const customAlertMessage = document.getElementById(
  "custom-alert-message-register"
);
const customAlertClose = document.getElementById("custom-alert-close");
const idInput = document.getElementById("empresaId2");
const emailInput = document.getElementById("email2");
const passwordInput = document.getElementById("password2");
const registerForm = document.getElementById("register-form");
const resetPasswordForm = document.getElementById("reset-password-form");

registerForm.addEventListener("submit", function (event) {
  event.preventDefault();

  const email = emailInput.value.trim();
  const password = passwordInput.value.trim();
  const empresaId = idInput.value.trim();
  if (!empresaId) {
    showCustomAlert("Por favor, ingresa el ID de la empresa.");
    return;
  }

  if (!email || !email.includes("@")) {
    showCustomAlert("Por favor, ingresa un correo electrónico válido.");
    return;
  }

  if (!password) {
    showCustomAlert("Por favor, ingresa una contraseña.");
    return;
  }

  //  Alerta registro exitoso
  showCustomAlert("Te has registrado exitosamente");
});

function showCustomAlert(message) {
  customAlertMessage.textContent = message;
  customAlert.style.display = "block";
}

function cerrarModal() {
  customAlert.style.display = "none";
  emailInput.value = "";
  passwordInput.value = "";
  idInput.value = "";
}

customAlertClose.addEventListener("click", cerrarModal);

document.addEventListener("keydown", function (event) {
  if (event.key === "Escape") {
    cerrarModal();
  }
});
document.addEventListener("click", function (event) {
  if (event.target === customAlert) {
    cerrarModal();
  }
});
