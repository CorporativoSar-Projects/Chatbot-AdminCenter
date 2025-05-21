// Función para manejar el estado activo de los botones
function handleButtonClick(event) {
  // Remover la clase 'active' de todos los botones
  var buttons = document.querySelectorAll(".boton1");
  buttons.forEach((button) => button.classList.remove("active"));

  // Añadir la clase 'active' al botón presionado
  event.target.classList.add("active");
}

function impMenu1(event) {
  handleButtonClick(event);
  // Contenido de la función impMenu1
  var stringMenu = `
        <div class="container-crear-conver">
            <label class="label-nombrechat2">Mensaje inicial de la conversación<span class="false-span" style="color: white;">1234567891011121314151617181920</span></label><br>
            <input type="text" name="inp-mensaje-usuario" id="inp-mensaje-usuario" placeholder="Escribe"
                class="inp-mensaje-usuario-crear" required><br>
       
            
            <label class="label-nombrechat">Origen de búsqueda</label><br>
            <input type="text" name="inp-columna" id="inp-columna" placeholder="Escribe"
                class="input-columna-crear" required><br>

            <label class="label-nombrechat">URL del informe</label><br>
            <input type="url" name="inp-url-informe" id="inp-url-informe" placeholder="https://ejemplo.com"
                    class="input-columna-crear"><br>

            
        </div>

    `;
  document.getElementById("imprimir").innerHTML = stringMenu;

  //Se movio el bloque de código comentado de la url porque el marcado que devuelve js no permite comentarios

  /* <label class="label-nombrechat">URL de origen de datos</label><br>
            <input type="text" name="inp-columna" id="inp-url" placeholder="Escribe"
                class="input-columna-crear" required><br> */
}

function impMenu2(event) {
  handleButtonClick(event);
  // Contenido de la función impMenu2
  var stringMenu = `
        <div class="container-crear-conver">
            <label class="label-nombrechat2">Mensaje inicial de la conversación<span class="false-span" style="color: white;">1234567891011121314151617181920</span></label><br>
            <input type="text" name="inp-mensaje-usuario" id="inp-mensaje-usuario" placeholder="Escribe"
                class="inp-mensaje-usuario-crear" required><br>
          
            <label class="label-nombrechat">Origen de búsqueda</label><br>
            <input type="text" name="inp-columna" id="inp-columna" placeholder="Escribe"
                class="input-columna-crear" required><br>

             <label class="label-nombrechat">URL del informe</label><br>
            <input type="url" name="inp-url-informe" id="inp-url-informe" placeholder="https://ejemplo.com"
                    class="input-columna-crear"><br>
        </div>
    `;
  document.getElementById("imprimir").innerHTML = stringMenu;
  //Se movio el bloque de código comentado de la url porque el marcado que devuelve js no permite comentarios

  /*   <label class="label-nombrechat">URL de origen de datos</label><br>
            <input type="text" name="inp-columna" id="inp-url" placeholder="Escribe"
                class="input-columna-crear" required><br> */
}

function impMenu3(event) {
  handleButtonClick(event);
  // Contenido de la función impMenu3
  var stringMenu = `
        <div class="container-crear-conver">
            <label class="label-nombrechat2">Mensaje inicial de la conversación<span class="false-span" style="color: white;">1234567891011121314151617181920</span></label><br>
            <input type="text" name="inp-mensaje-usuario" id="inp-mensaje-usuario" placeholder="Escribe"
                class="inp-mensaje-usuario-crear" required><br>
           
            
            <label class="label-nombrechat">Origen de búsqueda</label><br>
            <input type="text" name="inp-columna" id="inp-columna" placeholder="Escribe"
                class="input-columna-crear" required><br>
              <!--
            <label class="label-nombrechat">URL del informe</label><br>
            <input type="url" name="inp-url-informe" id="inp-url-informe" placeholder="https://ejemplo.com"
                    class="input-columna-crear"><br>*/
                     -->
        </div>
    `;
  document.getElementById("imprimir").innerHTML = stringMenu;
  //Se movio el bloque de código comentado de la url porque el marcado que devuelve js no permite comentarios

  /* <label class="label-nombrechat">URL de origen de datos</label><br>
            <input type="text" name="inp-columna" id="inp-url" placeholder="Escribe"
                class="input-columna-crear" required><br> */
}

// Añadir eventos a los botones
document.getElementById("boton1").addEventListener("click", impMenu1);
document.getElementById("boton2").addEventListener("click", impMenu2);
document.getElementById("boton3").addEventListener("click", impMenu3);
