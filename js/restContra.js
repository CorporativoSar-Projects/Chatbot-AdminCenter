// Obtener elementos del DOM
const forgotPasswordLink = document.getElementById("link-restablecer");
const resetPasswordForm = document.getElementById("reset-password-form");
const closeFormButton = document.getElementById("close-modal");
const resetForm = document.getElementById("reset-form");
const emailInput = document.getElementById("email");

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
    }
  });


  resetForm.addEventListener("submit", function (event) {
    event.preventDefault();

    const email = emailInput.value.trim();

    if (email) {
      alert("Se ha enviado una contraseña aleatoria a: " + email);
      cerrarModal();
    } else {
      alert("Por favor, ingresa un correo electrónico válido.");
    }
  });

//Función para generar contraseña aleatoria//

function generateRandomPassword(length = 12) {
  const charset =
    "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
  let password = "";
  for (let i = 0; i < length; i++) {
    const randomIndex = Math.floor(Math.random() * charset.length);
    password += charset[randomIndex];
  }
  return password;
}

console.log(generateRandomPassword());