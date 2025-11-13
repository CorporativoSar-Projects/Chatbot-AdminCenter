const userButton = document.getElementById("user-btn");
const userMenu = document.getElementById("dropdown-content");

// Inicialmente oculto
userMenu.style.display = "none";

let hideTimeout;

// Detectar dispositivo
const isMobileOrTablet = window.matchMedia("(max-width: 1110px)").matches;

// --- VERSIÓN ESCRITORIO (hover) ---
if (!isMobileOrTablet) {
  userButton.addEventListener("mouseenter", () => {
    clearTimeout(hideTimeout);
    userMenu.style.display = "block";
  });

  userMenu.addEventListener("mouseenter", () => {
    clearTimeout(hideTimeout);
  });

  userButton.addEventListener("mouseleave", scheduleHide);
  userMenu.addEventListener("mouseleave", scheduleHide);

  function scheduleHide() {
    hideTimeout = setTimeout(() => {
      if (!userButton.matches(":hover") && !userMenu.matches(":hover")) {
        userMenu.style.display = "none";
      }
    }, 150);
  }
}

// --- VERSIÓN MÓVIL / TABLET (click + scroll) ---
if (isMobileOrTablet) {
  userButton.addEventListener("click", (e) => {
    e.stopPropagation();
    const isVisible = userMenu.style.display === "block";
    userMenu.style.display = isVisible ? "none" : "block";
  });

  // Ocultar al hacer scroll (cuando se sube o baja el contenido)
  window.addEventListener("scroll", () => {
    if (userMenu.style.display === "block") {
      userMenu.style.display = "none";
    }
  });

  // Ocultar al tocar o hacer clic fuera del menú
  document.addEventListener("click", (e) => {
    if (!userButton.contains(e.target) && !userMenu.contains(e.target)) {
      userMenu.style.display = "none";
    }
  });
}
