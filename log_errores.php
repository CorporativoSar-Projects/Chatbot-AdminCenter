<?php
include 'modelo/consultas_menu.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/bootstrap.min.css" />
  <link rel="stylesheet" href="css/sty.css" />
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet" />
  <title>Errores API</title>
  <link rel="shortcut icon" href="img/Logo_cabeza.svg" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
  <div class="rectangulo-container">
    <img src="img/Logo_cabeza.svg" width="70px" alt="Logo" class="img-logo-chiq" />
  </div>

  <header>
    <!--<nav class="navbar">
      <ul class="filas">
        <li><a href="menu.php" class="txt-home">Home</a></li>
        <li><a href="vacantes.php" class="txt-home">Vacantes</a></li>
        <li><a href="chatbot.php" class="txt-home">ChatBots</a></li>
        <li><a href="errores_api.php" class="txt-home active">Errores API</a></li>
      </ul>
    </nav>-->

    <div class="user-dropdown">
      <div class="cont-btn-user" id="close-btn-user">
        <button class="btn-user" id="user-btn">
          <img src="img/user.png" width="30" alt="User Icon" />
        </button>
      </div>
      <div class="dropdown-content" id="dropdown-content">
        <div class="d-flex align-items-center px-3 user-info">
          <img src="img/user.png" width="40" alt="User Icon" />
          <div class="div-user">
            <strong><?php echo $_SESSION['nombre_adm'] . ' ' . $_SESSION['apellidop_adm']; ?></strong><br />
            <small><?php echo $_SESSION['correo_adm']; ?></small>
          </div>
        </div>
        <div class="dropdown-links">
          <a class="a1" href="cerrarSesion.php">Cerrar Sesión</a>
        </div>
      </div>
    </div>
  </header>

  <br>
  <br>
  <br>
  <br>
  <main id="contenidoPrincipal" class="container mt-4">

    <h2 class="mb-4">Errores de la API</h2>

    <div class="table-responsive">
      <table id="errores-table" class="table table-striped table-bordered">
        <thead class="thead-dark">
          <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Session ID</th>
            <th>Mensaje</th>
            <th>Error</th>
            <th>Código</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

  </main>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const API_BASE = "http://localhost:8000/api";
    const csrftoken = document.cookie.split('; ').find(row => row.startsWith('csrftoken='))?.split('=')[1];

    async function cargarErroresAPI() {
      try {
        const res = await fetch(`${API_BASE}/errores/`, { headers: { "X-CSRFToken": csrftoken } });
        if (!res.ok) throw new Error(await res.text());

        const data = await res.json();
        const tbody = document.querySelector("#errores-table tbody");
        tbody.innerHTML = "";

        data.forEach(err => {
          const tr = document.createElement("tr");
          tr.innerHTML = `
            <td>${err.id}</td>
            <td>${err.user__username || 'Anon'}</td>
            <td>${err.session_id || '-'}</td>
            <td>${err.user_message}</td>
            <td>${err.error_text}</td>
            <td>${err.error_code || '-'}</td>
            <td>${new Date(err.created_at).toLocaleString()}</td>
          `;
          tbody.appendChild(tr);
        });

      } catch (error) {
        console.error("Error cargando los errores de la API:", error);
        document.querySelector("#errores-table tbody").innerHTML = `<tr><td colspan="7" class="text-center text-danger">No se pudieron cargar los errores.</td></tr>`;
      }
    }

    document.addEventListener("DOMContentLoaded", cargarErroresAPI);
  </script>

</body>
</html>
