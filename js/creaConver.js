

// Función para manejar el estado activo de los botones
function handleButtonClick(event) {
    // Remover la clase 'active' de todos los botones
    var buttons = document.querySelectorAll('.boton1');
    buttons.forEach(button => button.classList.remove('active'));

    // Añadir la clase 'active' al botón presionado
    event.target.classList.add('active');
}

function impMenu1(event) {
    handleButtonClick(event);
    // Contenido de la función impMenu1
    var stringMenu = `
        <div class="container-crear-conver">
            <label class="label-nombrechat2">Escribe el mensaje que verá el usuario al
                iniciar la conversación</label><br>
            <input type="text" name="inp-mensaje-usuario" id="inp-mensaje-usuario" placeholder="Escribe"
                class="inp-mensaje-usuario-crear" required><br>

            <div class="container-archivo">
                <label for="archivoLogotipo">Selecciona un archivo</label><br>
                <div class="select-archivo">
                    <input type="file" id="archivo" accept="" onchange="previsualizarImagen()"
                        style="display: none;">
                    <button class="btn-select-archivo"
                        onclick="document.getElementById('archivoLogotipo').click()">
                        <span id="nombreArchivo" class="nombre-archivo">Seleccione un archivo</span>
                        <img src="img/add1.png" width="20" alt="Edit ChatBot">
                    </button>
                </div>
            </div>

            <label class="label-nombrechat">Define el nombre de la columna </label><br>
            <input type="text" name="inp-columna" id="inp-columna" placeholder="Escribe"
                class="input-columna-crear" required><br>
        </div>
    `;
    document.getElementById("imprimir").innerHTML = stringMenu;
}

function impMenu2(event) {
    handleButtonClick(event);
    // Contenido de la función impMenu2
    var stringMenu = `
        <div class="container-crear-conver">
            <label class="label-nombrechat2">2Escribe el mensaje que verá el usuario al
                iniciar la conversación</label><br>
            <input type="text" name="inp-mensaje-usuario" id="inp-mensaje-usuario" placeholder="Escribe"
                class="inp-mensaje-usuario-crear" required><br>

            <div class="container-archivo">
                <label for="archivoLogotipo">Selecciona un archivo</label><br>
                <div class="select-archivo">
                    <input type="file" id="archivo" accept="" onchange="previsualizarImagen()"
                        style="display: none;">
                    <button class="btn-select-archivo"
                        onclick="document.getElementById('archivoLogotipo').click()">
                        <span id="nombreArchivo" class="nombre-archivo">Seleccione un archivo</span>
                        <img src="img/add1.png" width="20" alt="Edit ChatBot">
                    </button>
                </div>
            </div>

            <label class="label-nombrechat">Define el nombre de la columna </label><br>
            <input type="text" name="inp-columna" id="inp-columna" placeholder="Escribe"
                class="input-columna-crear" required><br>
        </div>
    `;
    document.getElementById("imprimir").innerHTML = stringMenu;
}

function impMenu3(event) {
    handleButtonClick(event);
    // Contenido de la función impMenu3
    var stringMenu = `
        <div class="container-crear-conver">
            <label class="label-nombrechat2">3Escribe el mensaje que verá el usuario al
                iniciar la conversación</label><br>
            <input type="text" name="inp-mensaje-usuario" id="inp-mensaje-usuario" placeholder="Escribe"
                class="inp-mensaje-usuario-crear" required><br>

            <div class="container-archivo">
                <label for="archivoLogotipo">Selecciona un archivo</label><br>
                <div class="select-archivo">
                    <input type="file" id="archivo" accept="" onchange="previsualizarImagen()"
                        style="display: none;">
                    <button class="btn-select-archivo"
                        onclick="document.getElementById('archivoLogotipo').click()">
                        <span id="nombreArchivo" class="nombre-archivo">Seleccione un archivo</span>
                        <img src="img/add1.png" width="20" alt="Edit ChatBot">
                    </button>
                </div>
            </div>

            <label class="label-nombrechat">Define el nombre de la columna </label><br>
            <input type="text" name="inp-columna" id="inp-columna" placeholder="Escribe"
                class="input-columna-crear" required><br>
        </div>
    `;
    document.getElementById("imprimir").innerHTML = stringMenu;
}

// Añadir eventos a los botones
document.getElementById('boton1').addEventListener('click', impMenu1);
document.getElementById('boton2').addEventListener('click', impMenu2);
document.getElementById('boton3').addEventListener('click', impMenu3);
