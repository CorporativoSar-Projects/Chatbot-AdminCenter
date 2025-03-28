const userButton = document.getElementById("user-btn");
const userMenu = document.getElementById("dropdown-content");

userButton.addEventListener("click", function () {
  if (userMenu.style.display === "block") {
    userMenu.style.display = "none";
  } else {
    userMenu.style.display = "block";
  }
});
