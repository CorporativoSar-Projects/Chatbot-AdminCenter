const forgotPasswordLink = document.getElementById("link-restablecer");
const resetPasswordForm = document.getElementById("reset-password-form");
const closeFormButton = document.getElementById("close-modal");
const resetForm = document.getElementById("reset-form");
const emailInput = document.getElementById("email");
const customAlert = document.getElementById("custom-alert");
const customAlertMessage = document.getElementById("custom-alert-message");
const customAlertClose = document.getElementById("custom-alert-close");

forgotPasswordLink.addEventListener("click", function (event) {
  event.preventDefault();
  emailInput.value = "";
  resetPasswordForm.style.display = "flex";
});

function cerrarModal() {
  resetPasswordForm.style.display = "none";
}

closeFormButton.addEventListener("click", cerrarModal);

window.addEventListener("click", function (event) {
  if (event.target === resetPasswordForm) {
    cerrarModal();
  }
});

document.addEventListener("keydown", function (event) {
  if (event.key === "Escape") {
    cerrarModal();

    if (customAlert.style.display === "block") {
      customAlert.style.display = "none";
    }
  }
});

resetForm.addEventListener("submit", function (event) {
  event.preventDefault();

  const email = emailInput.value.trim();
  if (!email) {
    showCustomAlert("Por favor, ingresa un correo electrónico válido.");
    return;
  }

  const newPassword = generateRandomPassword();
  console.log({ email, newPassword });
  fetch("send-reset-password.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ email, newPassword }),
  })
    .then((response) => response.text())
    .then((text) => {
      console.log("Respuesta del servidor (texto):", text);
      const data = JSON.parse(text);
      console.log("Respuesta del servidor (JSON):", data);
      if (data.success) {
        showCustomAlert(`Se ha enviado la contraseña a: ${email}`);
      } else {
        showCustomAlert("Error al enviar el correo electrónico.");
      }
    })
    .catch((error) => console.error("Error:", error));
});

function showCustomAlert(message) {
  customAlertMessage.textContent = message;
  customAlert.style.display = "block";
}

customAlertClose.addEventListener("click", function () {
  customAlert.style.display = "none";
});

customAlert.addEventListener("click", function (event) {
  if (event.target === customAlert) {
    customAlert.style.display = "none";
  }
});

// Función para generar contraseña aleatoria
function generateRandomPassword(length = 12) {
  const charset =
    "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
  return Array.from(
    { length },
    () => charset[Math.floor(Math.random() * charset.length)]
  ).join("");
}
