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
                    description: 'Aquí puedes agregar un nuevo asistente virtual para tu empresa.',
                    side: "left",
                    align: 'center'
                }
            },
            {
                element: '.tour-card-chatbot:first-child',
                popover: {
                    title: 'Tus Chatbots',
                    description: 'Aquí aparecerán tus asistentes. Haz clic en uno para editarlo.',
                    side: "bottom",
                    align: 'start'
                }
            }
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
