document.addEventListener("DOMContentLoaded", function () {

    const driver = window.driver.js.driver;
    const path   = window.location.pathname;

    /* 
      La variable "window.appData" existe en menu.php. En otras páginas se lee de
       localStorage como fallback para mantener consistencia durante
       la navegación del tour.                                        */

    let nombrePlan = 'basico';
    if (window.appData && window.appData.nombrePlan) {
        nombrePlan = window.appData.nombrePlan;
        localStorage.setItem('ixah_plan_usuario', nombrePlan);
    } else {
        nombrePlan = localStorage.getItem('ixah_plan_usuario') || 'basico';
    }

    const PLANES_PLUS = ['plusMensual', 'plusAnual'];
    const esPlus      = PLANES_PLUS.includes(nombrePlan);

    const baseVisto  = localStorage.getItem('ixah_tour_visto') === 'true';
    const baseActivo = localStorage.getItem('ixah_tour_activo') === 'true';
    const debeEjecutarBase = !baseVisto || baseActivo;

    const plusVisto  = localStorage.getItem('ixah_plus_tour_visto') === 'true';
    const plusActivo = localStorage.getItem('ixah_plus_tour_activo') === 'true';
    const debeEjecutarPlus = esPlus && (!plusVisto || plusActivo) && baseVisto;

    const btnGuia = document.getElementById("btnGuiaFlotante");

    // Si ningún tour aplica no hace nada
    if (!debeEjecutarBase && !debeEjecutarPlus) return;

    if (btnGuia && (debeEjecutarBase || debeEjecutarPlus)) {
        btnGuia.style.display = "none";
    }

    function mostrarMenuUsuario(mostrar) {
        const dd = document.getElementById('dropdown-content');
        if (dd) dd.style.display = mostrar ? 'block' : 'none';
    }

    /*detecta chatbot existente*/
    const existeChatbot  = document.querySelector('.container-chats') !== null;
    const btnAddDisabled = document.querySelector('.btn-add-chat[disabled]') !== null;

    let chatbotId = localStorage.getItem('ixah_chatbot_id') || null;
    if (!chatbotId && existeChatbot) {
        const linkEditar = document.querySelector('a[href*="estilo.php?id_chatbot="]');
        if (linkEditar) {
            chatbotId = new URLSearchParams(linkEditar.href.split('?')[1]).get('id_chatbot');
        }
    }

//los pasos de ambos planes son compartidos (si aplica) y se decide cuál mostrar según el estado del usuario
    const pasoChatbot = (existeChatbot || btnAddDisabled)
        ? {
            pagina: 'menu.php', element: '.btn-edit-chat',
            popover: {
                title: 'Editar tu Chatbot',
                description: 'Aquí puedes ver tu chatbot creado. Haz clic en el botón de editar para acceder a su configuración.',
                side: "bottom", align: 'center'
            }
          }
        : {
            pagina: 'menu.php', element: '.btn-add-chat',
            popover: {
                title: 'Crear nuevo Chatbot',
                description: 'Haz clic aquí para comenzar a configurar tu primer asistente virtual desde cero.',
                side: "bottom", align: 'center'
            }
          };

    const pasosBase = [
        {
            pagina: 'menu.php',
            popover: {
                title: 'Te damos la bienvenida a IXAH',
                description: 'Hola! Vemos que es tu primera vez aquí. ¿Qué te parece si damos una vuelta rápida?',
                side: "center", align: 'center'
            }
        },
        pasoChatbot,
        {
            pagina: 'menu.php', element: '#user-btn',
            popover: {
                title: 'Tu cuenta y ajustes',
                description: 'Accede a la información general y funcionalidades de tu cuenta en IXAH.',
                side: "left", align: 'center'
            },
            onHighlighted: () => mostrarMenuUsuario(true)
        },
        {
            pagina: 'menu.php', element: "a[href*='freshdesk']",
            popover: {
                title: 'Soporte técnico',
                description: 'Si tienes dudas o inconvenientes con el uso o funcionalidades de IXAH, aquí puedes contactar a nuestro equipo de soporte.',
                side: "left", align: 'center'
            },
            onHighlighted: () => mostrarMenuUsuario(true)
        },
        {
            pagina: 'menu.php', element: '#sftpLink',
            popover: {
                title: 'Conecta tus sistemas',
                description: 'Selecciona el tipo de integración que deseas configurar con IXAH:<br>• <b>Estándar:</b> Integración básica necesaria para el funcionamiento de IXAH. Aquí debes definir la URL de tu sitio de carrera.<br>• <b>SFTP:</b> Configura los datos de tu servidor SFTP para habilitar la integración automática entre IXAH y tu sistema ATS.',
                side: "left", align: 'center'
            },
            onHighlighted: () => mostrarMenuUsuario(true)
        },
        {
            pagina: 'menu.php', element: "a[href*='billing.stripe.com']",
            popover: {
                title: 'Gestiona tu suscripción',
                description: 'Administra tu suscripción: actualiza tu plan o cancélalo en cualquier momento.',
                side: "left", align: 'center'
            },
            onHighlighted: () => mostrarMenuUsuario(true),
            onDeselected:  () => mostrarMenuUsuario(false)
        },
        {
            pagina: 'estilo.php', element: '#inp_nombre',
            popover: {
                title: 'Nombra a tu asistente',
                description: 'Define el nombre que identificará a tu chatbot.',
                side: "bottom", align: 'start'
            }
        },
        {
            pagina: 'estilo.php', element: '#urlLogotipo',
            popover: {
                title: 'Personaliza el logotipo',
                description: 'Pega la URL de tu imagen y da click en guardar para que se actualice. Esto mostrará tu marca dentro de la interfaz del chat.',
                side: "top", align: 'start'
            }
        },
        {
            pagina: 'estilo.php', element: '#contenedor-colores-driver',
            popover: {
                title: 'Define los colores de marca',
                description: 'Personaliza tu chatbot con los colores que representan a tu empresa. Ajusta colores primarios, secundarios y de texto para completar la identidad.',
                side: "right", align: 'center'
            }
        },
        {
            pagina: 'burbuja.php', element: '#inp_burbuja',
            popover: {
                title: 'Mensaje de la burbuja',
                description: 'Define el mensaje que aparecerá en la burbuja del chat. Aprovecha para invitar a los candidatos a interactuar o postularse.',
                side: "bottom", align: 'start'
            }
        },
        {
            pagina: 'pantallaInicio.php', element: '#inp_saludo',
            popover: {
                title: 'Mensaje de bienvenida',
                description: 'Configura el mensaje de bienvenida que verán los candidatos al iniciar su primera conversación.',
                side: "right", align: 'start'
            }
        },
        {
            pagina: 'crearConversacion.php', element: '.container-pers3',
            popover: {
                title: 'Personaliza el flujo de temas de conversación',
                description: 'Configura aquí las opciones principales que guiarán la conversación con tus candidatos. Haz clic en las opciones para editar sus detalles.',
                side: "bottom", align: 'center'
            }
        },
        {
            pagina: 'crearConversacion.php', element: '#boton1',
            popover: {
                title: 'Buscar vacantes por categoría',
                description: 'Mensaje inicial de la conversación: Define el mensaje que visualiza el candidato cuando haya seleccionado la búsqueda de vacantes por categoría.<br>Origen de búsqueda: Es el nombre de la columna que contiene las categorías.<br>URL del informe: Define aquí la URL del informe que contiene las vacantes activas.',
                side: "bottom", align: 'center'
            },
            onClick: 'impMenu1'
        },
        {
            pagina: 'crearConversacion.php', element: '#boton2',
            popover: {
                title: 'Buscar vacantes por ubicación',
                description: 'Mensaje inicial de la conversación: Define el mensaje que visualiza el candidato cuando haya seleccionado la búsqueda de vacantes por ubicación.<br>Origen de búsqueda: Es el nombre de la columna que contiene las ubicaciones.',
                side: "bottom", align: 'center'
            },
            onClick: 'impMenu2'
        },
        {
            pagina: 'crearConversacion.php', element: '#boton3',
            popover: {
                title: 'Seguimiento de mi postulación',
                description: 'Mensaje inicial de la conversación: Define el mensaje que visualiza el candidato cuando haya seleccionado el seguimiento de su postulación.<br>Origen de búsqueda: Es el nombre de la columna que contiene los correos electrónicos de los candidatos postulados.<br>URL del informe: Define aquí la URL del informe que contiene el status de los candidatos postulados.',
                side: "bottom", align: 'center'
            },
            onClick: 'impMenu3'
        },
        {
            pagina: 'pantallaDespedida.php', element: '#inp_despedida',
            popover: {
                title: 'Mensaje de despedida',
                description: 'Define el mensaje que verán los candidatos cuando finalicen una conversación con el chatbot.',
                side: "top", align: 'start'
            }
        },
        {
            pagina: 'finalizar.php', element: '.container-input',
            popover: {
                title: 'Dominio de Implementación',
                description: 'Especifica la URL donde funcionará tu chatbot. Si ya registraste una URL, no será posible modificarla desde esta sección. Si necesitas actualizarla, contacta a nuestro equipo de soporte.',
                side: "bottom", align: 'center'
            }
        },
        {
            pagina: 'finalizar.php', element: '#myBtn .btn-text-Generar',
            popover: {
                title: 'Generar',
                description: '• <b>Generar:</b> obtén el código HTML que debes insertar en tu sitio web para tener a IXAH funcionando.',
                side: "top", align: 'center'
            }
        },
        {
            pagina: 'finalizar.php', element: '#btnInteractivo',
            popover: {
                title: 'Modo interactivo',
                description: '• <b>Modo interactivo:</b> previsualiza el funcionamiento y diseño de tu chatbot.',
                side: "top", align: 'center'
            }
        },
        {
            pagina: 'finalizar.php',
            popover: {
                title: esPlus ? '¡Base lista! Ahora exploramos el Plan Plus' : '¡Todo listo!',
                description: esPlus
                    ? 'Has completado la configuración base. A continuación te mostraremos las funcionalidades exclusivas de tu <b>Plan Plus</b>.'
                    : 'Has completado el recorrido con éxito. Ya tienes todo lo necesario para empezar a crear y personalizar tu primer ChatBot. ¡Manos a la obra!',
                side: "center", align: 'center'
            }
        }
    ];

//los sig pasos solo para el plan plus y les hace saber a lasdemas páginas
    const pasosPlus = [
        {
            pagina: 'panel_admin_ia.php',
            popover: {
                title: 'Panel de Configuración IA',
                description: 'Sección <b>exclusiva del Plan Plus</b>. Desde aquí configuras el modelo de lenguaje, gestionas los prompts y monitoreas el consumo de tokens de tu cuenta.',
                side: "center", align: 'center'
            }
        },
        {
            pagina: 'panel_admin_ia.php',
            element: '.nav-link[href="#modelos"], a[data-section="modelos"], #modelos-ia-section, .sidebar-link:first-child',
            popover: {
                title: 'Modelos IA',
                description: 'Selecciona y configura el modelo de inteligencia artificial (p. ej. GPT-4) que procesará las consultas de lenguaje natural. Con Plan Plus tienes <b>hasta 10 consultas diarias por usuario</b>.',
                side: "right", align: 'center'
            }
        },
        {
            pagina: 'panel_admin_ia.php',
            element: '.nav-link[href="#prompts"], a[data-section="prompts"], #prompts-section',
            popover: {
                title: 'Gestión de Prompts',
                description: 'Crea y edita las plantillas de instrucciones que definen cómo responde la IA en cada tipo de consulta: descripción de vacantes, seguimiento de candidatos y más.',
                side: "right", align: 'center'
            }
        },
        {
            pagina: 'panel_admin_ia.php',
            element: '.nav-link[href="#revisiones"], a[data-section="revisiones"], #revisiones-section',
            popover: {
                title: 'Revisiones',
                description: 'Consulta el historial de revisiones realizadas por la IA. Aquí puedes auditar las respuestas generadas y detectar posibles mejoras en tus prompts.',
                side: "right", align: 'center'
            }
        },
        {
            pagina: 'panel_admin_ia.php',
            element: '.nav-link[href="#tokens"], a[data-section="tokens"], #tokens-section',
            popover: {
                title: 'Monitoreo de Tokens',
                description: 'Consulta el consumo acumulado de tokens de tu cuenta. Mantén un seguimiento del uso para optimizar las consultas de lenguaje natural y evitar alcanzar el límite.',
                side: "right", align: 'center'
            }
        },

        /*para la pagina de vacantes*/
        {
            pagina: 'vacantes.php',
            popover: {
                title: 'Módulo de Vacantes y Candidatos',
                description: 'Sección <b>exclusiva del Plan Plus</b>. Visualiza y gestiona todas las vacantes activas y los candidatos postulados a través de IXAH.',
                side: "center", align: 'center'
            }
        },
        {
            pagina: 'vacantes.php',
            element: '.nav-tabs, .tab-content, #vacantes-tab, #candidatos-tab',
            popover: {
                title: 'Vacantes y Candidatos',
                description: 'Cambia entre las pestañas <b>Vacantes</b> y <b>Candidatos</b> para consultar el listado de puestos activos y revisar a los candidatos postulados a cada uno.',
                side: "bottom", align: 'center'
            }
        },

        /*cierre del plan plus*/
        {
            pagina: 'vacantes.php',
            popover: {
                title: '¡Todo listo con tu Plan Plus!',
                description: 'Has completado el recorrido completo. Ya conoces todas tus funcionalidades exclusivas: <b>configuración IA, prompts, tokens y gestión de vacantes</b>. ¡Manos a la obra!',
                side: "center", align: 'center'
            }
        }
    ];

       // lo sig decide qué tour correr según el estado actual del usuario para ahorrar el código


    // Páginas del tour base
    const paginasBase = [
        'menu.php','estilo.php','burbuja.php','pantallaInicio.php',
        'crearConversacion.php','pantallaDespedida.php','finalizar.php'
    ];

    // Páginas del tour Plus
    const paginasPlus = ['panel_admin_ia.php','vacantes.php'];

    // Todas las páginas posibles
    const todasLasPaginas = [...paginasBase, ...paginasPlus];

    // Detectar página actual
    let paginaActual = null;
    for (const p of todasLasPaginas) {
        if (path.includes(p)) { paginaActual = p; break; }
    }
    if (!paginaActual && (path === '/' || path.endsWith('/'))) paginaActual = 'menu.php';
    if (!paginaActual) return;

    const enPaginaBase = paginasBase.includes(paginaActual);
    const enPaginaPlus = paginasPlus.includes(paginaActual);

    // Si estamos en página Plus pero el tour base no está completo, no ejecutar Plus aún
    if (enPaginaPlus && !baseVisto && !plusActivo) return;

    // Determina qué tour ejecutar en esta página
    const ejecutarBase = debeEjecutarBase && enPaginaBase;
    const ejecutarPlus = debeEjecutarPlus && enPaginaPlus;

    if (!ejecutarBase && !ejecutarPlus) return;

    const todosLosPasos = ejecutarPlus ? pasosPlus : pasosBase;
    const prefijo       = ejecutarPlus ? 'ixah_plus' : 'ixah';

    const navMapBase = {
        'menu.php':              (id) => id ? `estilo.php?id_chatbot=${id}` : 'estilo.php?nuevo=1',
        'estilo.php':            () => 'burbuja.php',
        'burbuja.php':           () => 'pantallaInicio.php',
        'pantallaInicio.php':    () => 'crearConversacion.php',
        'crearConversacion.php': () => 'pantallaDespedida.php',
        'pantallaDespedida.php': () => 'finalizar.php',
        'finalizar.php':         () => esPlus ? 'panel_admin_ia.php' : null
    };

    const navMapPlus = {
        'panel_admin_ia.php': () => 'vacantes.php',
        'vacantes.php':       () => null
    };

    const navMapBaseReverse = {
        'estilo.php':            () => 'menu.php',
        'burbuja.php':           () => 'estilo.php',
        'pantallaInicio.php':    () => 'burbuja.php',
        'crearConversacion.php': () => 'pantallaInicio.php',
        'pantallaDespedida.php': () => 'crearConversacion.php',
        'finalizar.php':         () => 'pantallaDespedida.php'
    };

    const navMapPlusReverse = {
        'panel_admin_ia.php': () => 'finalizar.php',
        'vacantes.php':       () => 'panel_admin_ia.php'
    };

    const navMap        = ejecutarPlus ? navMapPlus        : navMapBase;
    const navMapReverse = ejecutarPlus ? navMapPlusReverse : navMapBaseReverse;

    /*Índice actual*/
    const indiceActual = parseInt(localStorage.getItem(`${prefijo}_tour_step`) || '0');

    const pasosEstaPagina = todosLosPasos
        .map((paso, idx) => ({ ...paso, indiceGlobal: idx }))
        .filter(paso => paso.pagina === paginaActual && paso.indiceGlobal >= indiceActual);

    if (pasosEstaPagina.length === 0) return;

    /* abre menú de usuario si aplica */
    const elementosMenu = ['#user-btn',"a[href*='freshdesk']",'#sftpLink',"a[href*='billing.stripe.com']"];
    if (paginaActual === 'menu.php' && elementosMenu.includes(pasosEstaPagina[0]?.element)) {
        mostrarMenuUsuario(true);
    }

    if (paginaActual === 'crearConversacion.php' && pasosEstaPagina[0]?.onClick) {
        const fn = window[pasosEstaPagina[0].onClick];
        if (typeof fn === 'function') fn(new Event('click'));
    }
    /* bloquear modal en finalizar*/
    if (paginaActual === 'finalizar.php' && typeof $ !== 'undefined') {
        $('#myModal').on('show.bs.modal', function (e) {
            if (localStorage.getItem(`${prefijo}_tour_activo`) === 'true') {
                e.preventDefault(); e.stopPropagation(); return false;
            }
        });
    }

    const stepsDriver = pasosEstaPagina.map(paso => {
        const step = { popover: paso.popover };
        if (paso.element) {
            step.element = paso.element;
            if (paso.onClick) {
                step.onHighlighted = () => {
                    const fn = window[paso.onClick];
                    if (typeof fn === 'function') fn(new Event('click'));
                };
            }
        }
        if (paso.onHighlighted) step.onHighlighted = paso.onHighlighted;
        if (paso.onDeselected)  step.onDeselected  = paso.onDeselected;
        return step;
    });

    // filtrar pasos sin elemento en el DOM 
    const stepsValidos = stepsDriver.filter(step => {
        if (!step.element) return true;
        const selectores = step.element.split(',').map(s => s.trim());
        const existe = selectores.some(sel => document.querySelector(sel));
        if (!existe) console.warn('[IXAH Tour] Elemento no encontrado:', step.element);
        return existe;
    });

    // ajustar el element al primer selector válido (para driver.js)
    stepsValidos.forEach((step, i) => {
        if (step.element && step.element.includes(',')) {
            const selectores = step.element.split(',').map(s => s.trim());
            step.element = selectores.find(sel => document.querySelector(sel)) || selectores[0];
        }
    });

    if (stepsValidos.length === 0) {
        console.warn('[IXAH Tour] Sin pasos válidos en:', paginaActual);
        return;
    }

    const ultimoIndice = pasosEstaPagina[pasosEstaPagina.length - 1].indiceGlobal;
    const esUltimoPaso = ultimoIndice === todosLosPasos.length - 1;

    /*instanciar driver*/
    const driverObj = driver({
        showProgress: true,
        animate: true,
        allowClose: true,
        nextBtnText: 'Siguiente',
        prevBtnText: 'Atrás',
        doneBtnText: esUltimoPaso ? 'Finalizar' : 'Siguiente Sección',
        steps: stepsValidos,

        onDestroyStarted: () => {
            localStorage.setItem(`${prefijo}_tour_activo`, 'false');
            localStorage.setItem(`${prefijo}_tour_visto`, 'true');
            localStorage.removeItem(`${prefijo}_tour_step`);
            if (!ejecutarPlus) localStorage.removeItem('ixah_chatbot_id');
            mostrarMenuUsuario(false);
            const btn = document.getElementById("btnGuiaFlotante");
            if (btn) btn.style.display = "block";
            driverObj.destroy();
        },

        onNextClick: () => {
            const idx       = driverObj.getActiveIndex();
            const pasoActual = pasosEstaPagina[idx];

            localStorage.setItem(`${prefijo}_tour_step`, pasoActual.indiceGlobal + 1);
            if (!ejecutarPlus && chatbotId) localStorage.setItem('ixah_chatbot_id', chatbotId);

            if (idx < stepsValidos.length - 1) {
                driverObj.moveNext();
            } else {
                const siguientePaso = todosLosPasos[pasoActual.indiceGlobal + 1];
                if (siguientePaso) {
                    const id   = localStorage.getItem('ixah_chatbot_id') || chatbotId;
                    const dest = navMap[paginaActual];
                    const url  = dest ? dest(id) : null;
                    driverObj.destroy();
                    if (url) {
                        window.location.href = url;
                    } else if (ejecutarBase && esPlus) {
                        // Transición base a plan plus
                        localStorage.setItem('ixah_tour_visto', 'true');
                        localStorage.setItem('ixah_tour_activo', 'false');
                        localStorage.setItem('ixah_plus_tour_activo', 'true');
                        localStorage.removeItem('ixah_plus_tour_step');
                        window.location.href = 'panel_admin_ia.php';
                    }
                } else {
                    // tour completado
                    localStorage.setItem(`${prefijo}_tour_activo`, 'false');
                    localStorage.setItem(`${prefijo}_tour_visto`, 'true');
                    localStorage.removeItem(`${prefijo}_tour_step`);
                    driverObj.destroy();
                }
            }
        },

        onPrevClick: () => {
            const idx = driverObj.getActiveIndex();
            if (idx > 0) {
                localStorage.setItem(`${prefijo}_tour_step`, pasosEstaPagina[idx].indiceGlobal - 1);
                driverObj.movePrevious();
            } else {
                const anteriorGlobal = pasosEstaPagina[0].indiceGlobal - 1;
                if (anteriorGlobal >= 0) {
                    localStorage.setItem(`${prefijo}_tour_step`, anteriorGlobal);
                    const dest = navMapReverse[paginaActual];
                    driverObj.destroy();
                    if (dest) window.location.href = dest();
                }
            }
        }
    });

    setTimeout(() => driverObj.drive(), 500);
});

/* función global para relanzar el tour desde el botón flotante */
function activarRecorrido() {
    const btn = document.getElementById("btnGuiaFlotante");
    if (btn) btn.style.display = "none";

    const plan = localStorage.getItem('ixah_plan_usuario') || 'basico';
    const PLANES_PLUS = ['plusMensual','plusAnual'];
    const esPlus = PLANES_PLUS.includes(plan);

    // si es pla plus y ya vio el tour base muestra solo el tour Plus
    const baseVisto = localStorage.getItem('ixah_tour_visto') === 'true';
    if (esPlus && baseVisto) {
        localStorage.setItem('ixah_plus_tour_activo', 'true');
        localStorage.setItem('ixah_plus_tour_visto', 'false');
        localStorage.removeItem('ixah_plus_tour_step');
        window.location.href = 'panel_admin_ia.php';
    } else {
        //sino, se lanza el tour completo desde el inicio (ambos planes)
        localStorage.setItem('ixah_tour_activo', 'true');
        localStorage.setItem('ixah_tour_visto', 'false');
        localStorage.removeItem('ixah_tour_step');
        localStorage.removeItem('ixah_chatbot_id');
        if (esPlus) {
            localStorage.setItem('ixah_plus_tour_visto', 'false');
            localStorage.removeItem('ixah_plus_tour_step');
        }
        window.location.reload();
    }
}
window.activarRecorrido = activarRecorrido;
