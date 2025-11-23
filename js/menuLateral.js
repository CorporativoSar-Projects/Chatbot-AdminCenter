const userButton = document.getElementById("user-btn");
const userMenu = document.getElementById("dropdown-content");

// Inicialmente oculto
userMenu.style.display = "none";

// Detectar dispositivo
const isMobileOrTablet = window.matchMedia("(max-width: 1110px)").matches;

// -----------------------------------------------
// FUNCIÓN: Solo permitir clic EXACTO en el botón real
// -----------------------------------------------
function clickIsValid(e) {
  return (
    e.target === userButton ||      // clic directo al botón
    e.target.tagName === "IMG"      // clic directo en la imagen dentro del botón
  );
}

// --- LÓGICA GENERAL PARA TODOS (PC + MÓVIL) ---
userButton.addEventListener("click", (e) => {
  // SOLO abrir si el clic es válido
  if (!clickIsValid(e)) return;

  e.stopPropagation();

  const isVisible = userMenu.style.display === "block";
  userMenu.style.display = isVisible ? "none" : "block";
});

// Ocultar al hacer clic fuera del menú
document.addEventListener("click", (e) => {
  if (!userButton.contains(e.target) && !userMenu.contains(e.target)) {
    userMenu.style.display = "none";
  }
});

// Ocultar al hacer scroll
window.addEventListener("scroll", () => {
  if (userMenu.style.display === "block") {
    userMenu.style.display = "none";
  }
});

// Ocultar si se usa RePag / AvPag
document.addEventListener("keydown", (e) => {
  if (e.key === "PageUp" || e.key === "PageDown") {
    userMenu.style.display = "none";
  }
});
