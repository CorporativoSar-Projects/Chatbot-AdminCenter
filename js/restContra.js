const forgotPasswordLink = document.getElementById("link-restablecer");
const resetPasswordForm = document.getElementById("reset-password-form");
const closeFormButton = document.getElementById("close-modal");
const resetForm = document.getElementById("reset-form");
const emailInput = document.getElementById("email");
export const customAlert = document.getElementById("custom-alert");
const customAlertMessage = document.getElementById("custom-alert-message");
export const customAlertClose = document.getElementById("custom-alert-close");

/* Funcion para abir el formulario de restablecer contraseña */
forgotPasswordLink.addEventListener("click", function (event) {
  event.preventDefault();
  /* Cada vez que se envía se limpia el input del correo */
  emailInput.value = "";
  resetPasswordForm.style.display = "flex";
});

/* Se cierra el formulario de restablecimiento de contraseña */
export function cerrarModal() {
  resetPasswordForm.style.display = "none";
}

closeFormButton.addEventListener("click", cerrarModal);

/* Para cerrar el formulario de restablecimiento al hacer click afuera */
window.addEventListener("click", function (event) {
  if (event.target === resetPasswordForm) {
    cerrarModal();
  }
});

/* Para cerrar el formulario de restablecimiento al presionar la tecla ESC */
document.addEventListener("keydown", function (event) {
  if (event.key === "Escape") {
    cerrarModal();

    if (customAlert.style.display === "block") {
      customAlert.style.display = "none";
    }
  }
});

/* Función que envía el correo con la contraseña aleatoria */
resetForm.addEventListener("submit", function (event) {
  event.preventDefault();

  const email = emailInput.value.trim();
   const submitButton = resetForm.querySelector('button[type="submit"]'); // Botón de enviar


  if (!email) {
    /* Si el correo no es valido el correo aparecera un mensaje de error */
    showCustomAlert("Por favor, ingresa un correo electrónico válido.");
    return;
  }
  // 🔒 Desactivar el botón y mostrar estado de envío
   submitButton.disabled = true;
  const originalText = submitButton.textContent;
  const originalBg = submitButton.style.backgroundColor;
  submitButton.style.backgroundColor = "#7b7b7b";
  submitButton.textContent = "Enviando...";
  

  /* Función que vincula al archivo de php para enviar el correo con la contraseña aleatoria */
  const newPassword = generateRandomPassword();


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
     .catch((error) => {
      console.error("Error:", error);
      showCustomAlert("Ocurrió un error al enviar la solicitud.");
    })
    .finally(() => {
      //  Restaurar el botón después del envío
      submitButton.disabled = false;
      submitButton.textContent = originalText;
      submitButton.style.backgroundColor = originalBg;
    });
});
//Función general para cerrar la ventana al presionar el botón, hacer click afuera y con escape

export default function showCustomAlert(message) {
  return new Promise((resolve) => {
    customAlertMessage.innerText = message;
    customAlert.style.display = "block";

    customAlertClose.onclick = () => {
      customAlert.style.display = "none";
      resolve();
    };

    customAlert.addEventListener("click", function (event) {
      if (event.target === customAlert) {
        customAlert.style.display = "none";
        resolve();
      }
    });

    customAlert.addEventListener("keydown", function (event) {
      if (event.key === "Escape") {
        customAlert.style.display = "none";
        resolve();
      }
    });
  });
}

// Función para generar contraseña aleatoria
function generateRandomPassword(length = 12) {
  const charset =
    "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()";
  return Array.from(
    { length },
    () => charset[Math.floor(Math.random() * charset.length)]
  ).join("");
}
