// Obtener elementos del DOM
const forgotPasswordLink = document.getElementById("link-restablecer");
const resetPasswordForm = document.getElementById("reset-password-form");
const closeFormButton = document.getElementById("close-form");
const resetForm = document.getElementById("reset-form");

forgotPasswordLink.addEventListener("click", function (event) {
  event.preventDefault();
  resetPasswordForm.style.display = "block";
});

closeFormButton.addEventListener("click", function () {
  resetPasswordForm.style.display = "none";
});

resetForm.addEventListener("submit", function (event) {
  event.preventDefault();

  const email = document.getElementById("email").value;

  if (email) {
    alert("Se ha enviado un enlace de restablecimiento a: " + email);

    resetPasswordForm.style.display = "none";
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
