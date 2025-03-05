document.addEventListener("DOMContentLoaded", function () {
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has("error")) {
    Swal.fire({
      padding: 20,
      text: "Id, correo o contraseña incorrectos",
      confirmButtonText: "Aceptar",
      customClass: {
        popup: "modal-alert",
        title: "alert-message",
        confirmButton: "alert-close",
      },
    }).then(() => {
      window.location = "index.php";
    });
  }
});
