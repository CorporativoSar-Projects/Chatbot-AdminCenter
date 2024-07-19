$(document).ready(function () {
    var currentPath = window.location.pathname.split("/").pop();
    $('#menu li a').each(function () {
        if ($(this).attr('href') === currentPath) {
            $(this).parent().addClass('estas');
        }
    });
});

document.addEventListener("DOMContentLoaded", function() {
    // Obtener los botones
    var btnRegresar = document.getElementById("btnRegresar");
    var btnContinuar = document.getElementById("btnContinuar");

    // Agregar eventos de clic
    if (btnRegresar) {
        btnRegresar.addEventListener("click", function(event) {
            event.preventDefault();
            // Acción para el botón Regresar
            var currentNavItem = document.querySelector('.menu-pers li.estas');
            if (currentNavItem) {
                var prevLink = currentNavItem.previousElementSibling ? currentNavItem.previousElementSibling.querySelector('a') : null;
                if (prevLink) {
                    window.location.href = prevLink.href;
                }
            }
        });
    }

    if (btnContinuar) {
        btnContinuar.addEventListener("click", function(event) {
            event.preventDefault();
            // Acción para el botón Continuar
            var currentNavItem = document.querySelector('.menu-pers li.estas');
            if (currentNavItem) {
                var nextLink = currentNavItem.nextElementSibling ? currentNavItem.nextElementSibling.querySelector('a') : null;
                if (nextLink) {
                    window.location.href = nextLink.href;
                }
            }
        });
    }
});




// document.addEventListener("DOMContentLoaded", function() {
//     const colorInputs = document.querySelectorAll("input[type='color']");
    
//     colorInputs.forEach(input => {
//         const muestra = document.querySelector(`#muestra${input.id.charAt(0).toUpperCase() + input.id.slice(1)}`);
//         muestra.style.backgroundColor = input.value;

//         input.addEventListener("input", function() {
//             muestra.style.backgroundColor = input.value;
//         });
//     });
// });



// function actualizarColores() {
//     // Obtener los valores de color seleccionados
//     const colorPrimario = document.getElementById('colorPrimario').value;
//     const colorSecundario = document.getElementById('colorSecundario').value;
//     const colorAcento = document.getElementById('colorAcento').value;
//     const colorTexto = document.getElementById('colorTexto').value;
    

//     //  CSS
//     document.documentElement.style.setProperty('--color-primario', colorPrimario);
//     document.documentElement.style.setProperty('--color-secundario', colorSecundario);
//     document.documentElement.style.setProperty('--color-acento', colorAcento);
//     document.documentElement.style.setProperty('--color-texto', colorTexto);
    

//     // Ajustar colores específicos si es necesario
//     document.documentElement.style.setProperty('--color-texto-header', '#ffffff'); // Texto del header siempre blanco
//     document.documentElement.style.setProperty('--color-texto-boton', colorTexto); // Texto de los botones
//     document.documentElement.style.setProperty('--color-texto-hover', '#ffffff'); // Texto de los botones al pasar el ratón
// }



