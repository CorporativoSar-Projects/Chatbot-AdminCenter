document.addEventListener("DOMContentLoaded", function () {
    const driver = window.driver.js.driver;
    const path = window.location.pathname;

    // Verificar si el tour debe ejecutarse
    const tourActivo = localStorage.getItem('ixah_tour_activo') === 'true';
    const tourVisto = localStorage.getItem('ixah_tour_visto') === 'true';

    if (!tourActivo && tourVisto) {
        return;
    }

    // --- FUNCIONES AUXILIARES ---
    function mostrarMenuUsuario(mostrar) {
        const dropdown = document.getElementById('dropdown-content');
        if (dropdown) {
            dropdown.style.display = mostrar ? 'block' : 'none';
        }
    }

    // --- PASOS DEL TOUR COMPLETO ---
    const todosLosPasos = [
        // PASO 0 - Bienvenida (menu.php)
        {
            pagina: 'menu.php',
            popover: {
                title: 'Te damos la bienvenida a IXAH',
                description: '¡Hola! 👋 Vemos que es tu primera vez aquí. ¿Qué te parece si damos una vuelta rápida?',
                side: "center",
                align: 'center'
            }
        },
        // PASO 1 - Crear chatbot (menu.php)
        {
            pagina: 'menu.php',
            element: '.btn-add-chat',
            popover: {
                title: 'Crear nuevo Chatbot',
                description: 'Haz clic aquí para comenzar a configurar tu primer asistente virtual desde cero.',
                side: "bottom",
                align: 'center'
            }
        },
        // PASO 2 - Botón usuario (menu.php)
        {
            pagina: 'menu.php',
            element: '#user-btn',
            popover: {
                title: 'Tu cuenta y ajustes',
                description: 'Accede a la información general y funcionalidades de tu cuenta en IXAH.',
                side: "left",
                align: 'center'
            },
            onHighlighted: () => mostrarMenuUsuario(true)
        },
        // PASO 3 - Soporte (menu.php)
        {
            pagina: 'menu.php',
            element: "a[href*='freshdesk']",
            popover: {
                title: 'Soporte técnico',
                description: 'Si tienes dudas o inconvenientes con el uso o funcionalidades de IXAH, aquí puedes contactar a nuestro equipo de soporte.',
                side: "left",
                align: 'center'
            },
            onHighlighted: () => mostrarMenuUsuario(true)
        },
        // PASO 4 - Integraciones (menu.php)
        {
            pagina: 'menu.php',
            element: '#sftpLink',
            popover: {
                title: 'Conecta tus sistemas',
                description: 'Selecciona el tipo de integración que deseas configurar con IXAH: <br> • <b>Estándar:</b> Integración básica necesaria para el funcionamiento de IXAH. Aquí debes definir la URL de tu sitio de carrera. <br>• <b>SFTP:</b> Configura los datos de tu servidor SFTP para habilitar la integración automática entre IXAH y tu sistema ATS.',
                side: "left",
                align: 'center'
            },
            onHighlighted: () => mostrarMenuUsuario(true)
        },
        // PASO 5 - Plan (menu.php)
        {
            pagina: 'menu.php',
            element: "a[href*='billing.stripe.com']",
            popover: {
                title: 'Gestiona tu suscripción',
                description: 'Administra tu suscripción: actualiza tu plan o cancélalo en cualquier momento.',
                side: "left",
                align: 'center'
            },
            onHighlighted: () => mostrarMenuUsuario(true),
            onDeselected: () => mostrarMenuUsuario(false)
        },
        // PASO 6 - Nombre (estilo.php)
        {
            pagina: 'estilo.php',
            element: '#inp_nombre',
            popover: {
                title: 'Nombra a tu asistente',
                description: 'Define el nombre que identificará a tu chatbot.',
                side: "bottom",
                align: 'start'
            }
        },
        // PASO 7 - Logo (estilo.php)
        {
            pagina: 'estilo.php',
            element: '#urlLogotipo',
            popover: {
                title: 'Personaliza el logotipo',
                description: 'Pega la URL de tu imagen y da click en guardar para que se actualice. Esto mostrará tu marca dentro de la interfaz del chat.',
                side: "top",
                align: 'start'
            }
        },
        // PASO 8 - Colores (estilo.php) - UNIFICADO
        {
            pagina: 'estilo.php',
            element: '.nombre-colord',
            popover: {
                title: 'Define los colores de marca',
                description: 'Personaliza tu chatbot con los colores que representan a tu empresa. Ajusta colores primarios, secundarios y de texto para completar la identidad.',
                side: "right",
                align: 'center'
            }
        },
        // PASO 9 - Burbuja (burbuja.php)
        {
            pagina: 'burbuja.php',
            element: '#inp_burbuja',
            popover: {
                title: 'Mensaje de la burbuja',
                description: 'Define el mensaje que aparecerá en la burbuja del chat. Aprovecha para invitar a los candidatos a interactuar o postularse.',
                side: "bottom",
                align: 'start'
            }
        },
        // PASO 10 - Saludo (pantallaInicio.php)
        {
            pagina: 'pantallaInicio.php',
            element: '#inp_saludo',
            popover: {
                title: 'Mensaje de bienvenida',
                description: 'Configura el mensaje de bienvenida que verán los candidatos al iniciar su primera conversación.',
                side: "right",
                align: 'start'
            }
        },
        // PASO 11 - Título conversación (crearConversacion.php)
        {
            pagina: 'crearConversacion.php',
            element: '.container-pers3',
            popover: {
                title: 'Personaliza el flujo de temas de conversación',
                description: 'Configura aquí las opciones principales que guiarán la conversación con tus candidatos. Haz clic en las opciones para editar sus detalles.',
                side: "bottom",
                align: 'center'
            }
        },
        // PASO 12 - Botón 1 Categoría (crearConversacion.php)
        {
            pagina: 'crearConversacion.php',
            element: '#boton1',
            popover: {
                title: 'Buscar vacantes por categoría',
                description: 'Mensaje inicial de la conversación: Define el mensaje que visualiza el candidato cuando haya seleccionado la búsqueda de vacantes por categoría.<br>Origen de búsqueda: Es el nombre de la columna que contiene las categorías.<br>URL del informe: Define aquí la URL del informe que contiene las vacantes activas.',
                side: "bottom",
                align: 'center'
            },
            onClick: 'impMenu1'
        },
        // PASO 13 - Botón 2 Ubicación (crearConversacion.php)
        {
            pagina: 'crearConversacion.php',
            element: '#boton2',
            popover: {
                title: 'Buscar vacantes por ubicación',
                description: 'Mensaje inicial de la conversación: Define el mensaje que visualiza el candidato cuando haya seleccionado la búsqueda de vacantes por ubicación.<br>Origen de búsqueda: Es el nombre de la columna que contiene las ubicaciones.',
                side: "bottom",
                align: 'center'
            },
            onClick: 'impMenu2'
        },
        // PASO 14 - Botón 3 Seguimiento (crearConversacion.php)
        {
            pagina: 'crearConversacion.php',
            element: '#boton3',
            popover: {
                title: 'Seguimiento de mi postulación',
                description: 'Mensaje inicial de la conversación: Define el mensaje que visualiza el candidato cuando haya seleccionado el seguimiento de su postulación.<br>Origen de búsqueda: Es el nombre de la columna que contiene los correos electrónicos de los candidatos postulados.<br>URL del informe: Define aquí la URL del informe que contiene el status de los candidatos postulados.',
                side: "bottom",
                align: 'center'
            },
            onClick: 'impMenu3'
        },
        // PASO 15 - Despedida (pantallaDespedida.php)
        {
            pagina: 'pantallaDespedida.php',
            element: '#inp_despedida',
            popover: {
                title: 'Mensaje de despedida',
                description: 'Define el mensaje que verán los candidatos cuando finalicen una conversación con el chatbot.',
                side: "top",
                align: 'start'
            }
        },
        // PASO 16 - URL (finalizar.php)
        {
            pagina: 'finalizar.php',
            element: '.container-input',
            popover: {
                title: 'Dominio de Implementación',
                description: 'Especifica la URL donde funcionará tu chatbot. Si ya registraste una URL, no será posible modificarla desde esta sección. Si necesitas actualizarla, contacta a nuestro equipo de soporte.',
                side: "bottom",
                align: 'center'
            }
        },
        // PASO 17 - Generar (finalizar.php)
        //Posible separacion por problemas de contenedores en los botones
        {
            pagina: 'finalizar.php',
            element: '#myBtn',
            popover: {
                title: 'Generar y modo interactivo',
                description: '• <b>Generar:</b> obtén el código HTML que debes insertar en tu sitio web para tener a IXAH funcionando.',
                side: "top",
                align: 'center'
            }
        },

        // PASO 17.1 - Generar (finalizar.php)
        {
            pagina: 'finalizar.php',
            element: '#btnInteractivo',
            popover: {
                title: 'Generar y modo interactivo',
                description: '• <b>Modo interactivo:</b> previsualiza el funcionamiento y diseño de tu chatbot.',
                side: "top",
                align: 'center'
            }
        },
        // PASO 18 - Final (finalizar.php)
        {
            pagina: 'finalizar.php',
            popover: {
                title: '¡Todo listo!',
                description: 'Has completado el recorrido con éxito. Ya tienes todo lo necesario para empezar a crear y personalizar tu primer ChatBot. ¡Manos a la obra!',
                side: "center",
                align: 'center'
            }
        }
    ];

    let paginaActual = null;
    const paginas = ['menu.php', 'estilo.php', 'burbuja.php', 'pantallaInicio.php', 'crearConversacion.php', 'pantallaDespedida.php', 'finalizar.php'];
    
    for (let pagina of paginas) {
        if (path.includes(pagina)) {
            paginaActual = pagina;
            break;
        }
    }

    if (!paginaActual && (path === '/' || path.endsWith('/'))) {
        paginaActual = 'menu.php';
    }

    if (!paginaActual) return;
    let indiceActual = parseInt(localStorage.getItem('ixah_tour_step') || '0');

    const primerPasoEstaPagina = todosLosPasos.findIndex((p, idx) => p.pagina === paginaActual && idx >= indiceActual);
    
    if (primerPasoEstaPagina === -1) {
        const siguientePaso = todosLosPasos.find((p, idx) => idx > indiceActual);
        if (siguientePaso) {
            const navMap = {
                'menu.php': 'estilo.php?nuevo=1',
                'estilo.php': 'burbuja.php',
                'burbuja.php': 'pantallaInicio.php',
                'pantallaInicio.php': 'crearConversacion.php',
                'crearConversacion.php': 'pantallaDespedida.php',
                'pantallaDespedida.php': 'finalizar.php'
            };
            if (navMap[paginaActual]) {
                localStorage.setItem('ixah_tour_activo', 'true');
                window.location.href = navMap[paginaActual];
            }
        }
        return;
    }

    const pasosEstaPagina = todosLosPasos
        .map((paso, idx) => ({...paso, indiceGlobal: idx}))
        .filter(paso => paso.pagina === paginaActual && paso.indiceGlobal >= indiceActual);

    const stepsDriver = pasosEstaPagina.map(paso => {
        const step = {
            popover: paso.popover
        };
        
        if (paso.element) {
            step.element = paso.element;
            
            if (paso.onClick) {
                step.onHighlighted = () => {
                    if (typeof window[paso.onClick] === 'function') {
                        window[paso.onClick](new Event('click'));
                    }
                };
            }
        }
        
        if (paso.onHighlighted) step.onHighlighted = paso.onHighlighted;
        if (paso.onDeselected) step.onDeselected = paso.onDeselected;
        
        return step;
    });

    const stepsValidos = stepsDriver.filter((step, idx) => {
        if (!step.element) return true;
        const existe = document.querySelector(step.element);
        if (!existe) {
            console.warn('Elemento no encontrado:', step.element);
        }
        return existe !== null;
    });

    if (stepsValidos.length === 0) {
        console.warn('No hay pasos válidos en esta página');
        return;
    }

    // Iniciar tour
    localStorage.setItem('ixah_tour_activo', 'true');

    const driverObj = driver({
        showProgress: true,
        animate: true,
        allowClose: false,
        nextBtnText: 'Siguiente',
        prevBtnText: 'Atrás',
        doneBtnText: pasosEstaPagina[pasosEstaPagina.length - 1].indiceGlobal === todosLosPasos.length - 1 ? 'Finalizar' : 'Siguiente Sección',
        steps: stepsValidos,
        onNextClick: () => {
            const indicePasoActual = driverObj.getActiveIndex();
            const pasoActual = pasosEstaPagina[indicePasoActual];
            
            localStorage.setItem('ixah_tour_step', pasoActual.indiceGlobal + 1);
            
            if (indicePasoActual < stepsValidos.length - 1) {
                // Siguiente paso en esta página
                driverObj.moveNext();
            } else {
                // Último paso de esta página - ir a siguiente página
                const siguientePaso = todosLosPasos[pasoActual.indiceGlobal + 1];
                
                if (siguientePaso) {
                    const navMap = {
                        'menu.php': 'estilo.php?nuevo=1',
                        'estilo.php': 'burbuja.php',
                        'burbuja.php': 'pantallaInicio.php',
                        'pantallaInicio.php': 'crearConversacion.php',
                        'crearConversacion.php': 'pantallaDespedida.php',
                        'pantallaDespedida.php': 'finalizar.php'
                    };
                    
                    driverObj.destroy();
                    window.location.href = navMap[paginaActual];
                } else {
                    // Tour completado
                    localStorage.setItem('ixah_tour_activo', 'false');
                    localStorage.setItem('ixah_tour_visto', 'true');
                    localStorage.removeItem('ixah_tour_step');
                    driverObj.destroy();
                }
            }
        },
        onPrevClick: () => {
            const indicePasoActual = driverObj.getActiveIndex();
            
            if (indicePasoActual > 0) {
                // Retroceder dentro de esta página
                const pasoActual = pasosEstaPagina[indicePasoActual];
                localStorage.setItem('ixah_tour_step', pasoActual.indiceGlobal - 1);
                driverObj.movePrevious();
            } else {
                // Primer paso de esta página - ir a página anterior
                const pasoAnterior = todosLosPasos[pasosEstaPagina[0].indiceGlobal - 1];
                
                if (pasoAnterior) {
                    localStorage.setItem('ixah_tour_step', pasoAnterior.indiceGlobal);
                    
                    const navMapReverse = {
                        'estilo.php': 'menu.php',
                        'burbuja.php': 'estilo.php',
                        'pantallaInicio.php': 'burbuja.php',
                        'crearConversacion.php': 'pantallaInicio.php',
                        'pantallaDespedida.php': 'crearConversacion.php',
                        'finalizar.php': 'pantallaDespedida.php'
                    };
                    
                    driverObj.destroy();
                    window.location.href = navMapReverse[paginaActual];
                }
            }
        }
    });

    setTimeout(() => driverObj.drive(), 500);
});