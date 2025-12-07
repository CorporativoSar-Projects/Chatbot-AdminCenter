document.addEventListener("DOMContentLoaded", function () {

    // Verificar si ya se vio el tour
    if (localStorage.getItem('tour_ixah_visto') === 'si') {
        return;
    }

    const driver = window.driver.js.driver;

    const tour = driver({
        showProgress: true,
        animate: true,
        allowClose: false,

        nextBtnText: 'Siguiente',
        prevBtnText: 'Atrás',
        doneBtnText: 'Finalizar',

        steps: [
            {
                popover: {
                    title: 'Te damos la bienvenida a IXAH',
                    description: '¡Hola! 👋 Vemos que es tu primera vez aquí. ¿Qué te parece si damos una vuelta rápida?',
                    side: "center",
                    align: 'center',
                }
            },
            {
                element: '#tour-btn-crear',
                popover: {
                    title: 'Crear nuevo Chatbot',
                    description: 'Haz clic aquí para comenzar a configurar tu primer asistente virtual desde cero.',
                    side: "left",
                    align: 'center'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Tu cuenta y ajustes',
                    description: 'Accede a la información general y funcionalidades de tu cuenta en IXAH.',
                    side: "bottom",
                    align: 'start'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Soporte técnico',
                    description: 'Si tienes dudas o inconvenientes con el uso o funcionalidades de IXAH, aquí puedes contactar a nuestro equipo de soporte.',
                    side: "bottom",
                    align: 'start'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Conecta tus sistemas',
                    description: 'Selecciona el tipo de integración que deseas configurar con IXAH: \n • Estándar: Integración básica necesaria para el funcionamiento de IXAH. Aquí debes definir la URL de tu sitio de carrera. \n• SFTP: Configura los datos de tu servidor SFTP para habilitar la integración automática entre IXAH y tu sistema ATS.',
                    side: "bottom",
                    align: 'start'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Gestiona tu suscripción',
                    description: 'Administra tu suscripción: actualiza tu plan o cancélalo en cualquier momento.',
                    side: "bottom",
                    align: 'start'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Nombra a tu asistente',
                    description: 'Define el nombre que identificará a tu chatbot.',
                    side: "bottom",
                    align: 'start'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Personaliza el logotipo',
                    description: 'Pega la URL de tu imagen y da click en guardar para que se actualice. Esto mostrará tu marca dentro de la interfaz del chat.',
                    side: "bottom",
                    align: 'start'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Define los colores de marca',
                    description: 'Personaliza tu chatbot con los colores que representan a tu empresa.',
                    side: "bottom",
                    align: 'start'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Mensaje de la burbuja',
                    description: 'Define el mensaje que aparecerá en la burbuja del chat. Aprovecha para invitar a los candidatos a interactuar o postularse.',
                    side: "bottom",
                    align: 'start'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Mensaje de bienvenida',
                    description: 'Configura el mensaje de bienvenida que verán los candidatos al iniciar su primera conversación.',
                    side: "bottom",
                    align: 'start'
                }
            },
            //Aqui es donde las opciones e dividen en tres
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Personaliza el flujo de temas de conversación',
                    description: 'Configura aquí las opciones principales que guiarán la conversación con tus candidatos.',
                    side: "bottom",
                    align: 'start'
                }///tres botones para ir a alastre secines
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Define los colores de marca',
                    description: 'Mensaje inicial de la conversación: Define el mensaje que visualiza el candidato cuando haya seleccionado la búsqueda de vacantes por categoría. \n Origen de búsqueda: Es el nombre de la columna que contiene las categorías. \n URL del informe: Define aquí la URL del informe que contiene las vacantes activas.',
                    side: "bottom",
                    align: 'start'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Buscar vacantes por ubicación',
                    description: ' Mensaje inicial de la conversación: Define el mensaje que visualiza el candidato cuando haya seleccionado la búsqueda de vacantes por ubicación. Origen de búsqueda: Es el nombre de la columna que contiene las ubicaciones.',
                    side: "bottom",
                    align: 'start'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Seguimiento de mi postulación',
                    description: 'Mensaje inicial de la conversación: Define el mensaje que visualiza el candidato cuando haya seleccionado el seguimineto de su postulación. \n Origen de búsqueda: Es el nombre de la columna que contiene los correos electronicos de los candidatos postulados. \n URL del informe: Define aquí la URL del informe que contiene el status de los candidatos postulados.',
                    side: "bottom",
                    align: 'start'
                }
            },
            ///////////////////////////////////////////////////
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Mensaje de despedida',
                    description: 'Define el mensaje que verán los candidatos cuando finalicen una conversación con el chatbot.',
                    side: "bottom",
                    align: 'start'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Dominio de Implementación',
                    description: 'Especifica la URL donde funcionará tu chatbot. Si ya registraste una URL, no será posible modificarla desde esta sección. Si necesitas actualizarla, contacta a nuestro equipo de soporte.',
                    side: "bottom",
                    align: 'start'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Generar y modo interactivo',
                    description: '. Generar: obtén el código HTML que debes insertar en tu sitio web para tener a IXAH funcionando. \n . Modo interactivo: previsualiza el funcionamiento y diseño de tu chatbot. ',
                    side: "bottom",
                    align: 'start'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: '¡Todo listo!',
                    description: 'Has completado el recorrido con éxito. Ya tienes todo lo necesario para empezar a crear y personalizar tu primer ChatBot. ¡Manos a la obra!',
                    side: "bottom",
                    align: 'start'
                }
            },

        ],

        onPopoverRender: (popover, { state }) => {

            const stepIndex = state.activeIndex;
            const totalStepsReales = 2;

            const progressText = popover.wrapper.querySelector('.driver-popover-progress-text');

            if (stepIndex === 0) {

                if (progressText) progressText.style.display = 'none';

                const nextBtn = popover.footerButtons.querySelector('.driver-popover-next-btn');
                if (nextBtn) nextBtn.innerText = 'Comenzar';

                const prevBtn = popover.footerButtons.querySelector('.driver-popover-prev-btn');
                if (prevBtn) prevBtn.style.display = 'none';

                // --- CREACIÓN DEL BOTÓN ---
                const noBtn = document.createElement('button');
                noBtn.innerText = 'No, gracias';
                noBtn.style.cssText = `
                    background:transparent;
                    border:none;
                    color:#888;
                    cursor:pointer;
                    margin-right:auto;
                    font-size:13px;
                    font-weight:500;
                    text-decoration:underline;
                    font-family:"Montserrat", sans-serif;
                `;

                noBtn.onclick = () => {
                    localStorage.setItem('tour_ixah_visto', 'si');
                    tour.destroy();
                };

                // --- FIX COMPLETO ANTI-DUPLICADOS ---
                const existingNoBtn = Array.from(
                    popover.footerButtons.querySelectorAll("button")
                ).find(b => b.innerText.trim() === "No, gracias");

                if (!existingNoBtn) {
                    popover.footerButtons.insertBefore(noBtn, popover.footerButtons.firstChild);
                }

            } else {
                if (progressText) {
                    progressText.style.display = 'block';
                    progressText.innerText = `${stepIndex} de ${totalStepsReales}`;
                }
            }
        },

        onDestroyed: () => {
            localStorage.setItem('tour_ixah_visto', 'si');
        }
    });

    tour.drive();
});
