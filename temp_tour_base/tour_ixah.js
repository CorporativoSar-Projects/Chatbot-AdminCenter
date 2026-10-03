

document.addEventListener("DOMContentLoaded", function () {

    const driver = window.driver.js.driver;
    const path   = window.location.pathname;

    let nombrePlan = 'basico';
    if (window.appData && window.appData.nombrePlan) {
        nombrePlan = window.appData.nombrePlan;
        localStorage.setItem('ixah_plan_usuario', nombrePlan);
    } else {
        nombrePlan = localStorage.getItem('ixah_plan_usuario') || 'basico';
    }

    const PLANES_PLUS = ['plusMensual', 'plusAnual'];
    const esPlus      = PLANES_PLUS.includes(nombrePlan);

    const baseVisto   = localStorage.getItem('ixah_tour_visto') === 'true';
    const baseActivo  = localStorage.getItem('ixah_tour_activo') === 'true';
    const plusVisto   = localStorage.getItem('ixah_plus_tour_visto') === 'true';
    const plusActivo  = localStorage.getItem('ixah_plus_tour_activo') === 'true';

    const debeEjecutarBase = !baseVisto || baseActivo;
    const debeEjecutarPlus = esPlus && (!plusVisto || plusActivo);

    const btnGuia = document.getElementById("btnGuiaFlotante");

    // EL boron de guia flotante se oculta solo cuando el tour está corriendo
    if (btnGuia) btnGuia.style.display = "flex";

    if (!debeEjecutarBase && !debeEjecutarPlus) return;

    // Ocultar mientras el tour está activo
    if (btnGuia) btnGuia.style.display = "none";

    function mostrarMenuUsuario(mostrar) {
        const dd = document.getElementById('dropdown-content');
        if (dd) dd.style.display = mostrar ? 'block' : 'none';
    }

    let chatbotId = localStorage.getItem('ixah_chatbot_id') || null;
    if (!chatbotId) {
        const linkEditar = document.querySelector('a[href*="estilo.php?id_chatbot="]');
        if (linkEditar) chatbotId = new URLSearchParams(linkEditar.href.split('?')[1]).get('id_chatbot');
    }

    const existeChatbot  = document.querySelector('.container-chats') !== null;
    const btnAddDisabled = document.querySelector('.btn-add-chat[disabled]') !== null;

    const pasoChatbot = (existeChatbot || btnAddDisabled)
        ? { pagina: 'menu.php', element: '.btn-edit-chat',
            popover: { title: 'Editar tu Chatbot', description: 'Aquí puedes ver tu chatbot creado. Haz clic en el botón de editar para acceder a su configuración.', side:"bottom", align:'center' } }
        : { pagina: 'menu.php', element: '.btn-add-chat',
            popover: { title: 'Crear nuevo Chatbot', description: 'Haz clic aquí para comenzar a configurar tu asistente virtual.', side:"bottom", align:'center' } };

//Pasos compartidos entre el plan base y plan plus
    const pasosBase = [
        { pagina:'menu.php', popover:{ title:'Te damos la bienvenida a IXAH', description:'¡Hola! Vemos que es tu primera vez aquí. ¿Qué te parece si damos una vuelta rápida?', side:"center", align:'center' } },
        pasoChatbot,
        { pagina:'menu.php', element:'#user-btn', popover:{ title:'Tu cuenta y ajustes', description:'Accede a la información general y funcionalidades de tu cuenta en IXAH.', side:"left", align:'center' }, onHighlighted: () => mostrarMenuUsuario(true) },
        { pagina:'menu.php', element:"a[href*='freshdesk']", popover:{ title:'Soporte técnico', description:'Si tienes dudas con IXAH, aquí puedes contactar a nuestro equipo de soporte.', side:"left", align:'center' }, onHighlighted: () => mostrarMenuUsuario(true) },
        { pagina:'menu.php', element:'#sftpLink', popover:{ title:'Conecta tus sistemas', description:'Configura la integración <b>Estándar</b> (URL de tu sitio de carrera) o <b>SFTP</b> para sincronización automática con tu ATS.', side:"left", align:'center' }, onHighlighted: () => mostrarMenuUsuario(true) },
        { pagina:'menu.php', element:"a[href*='billing.stripe.com']", popover:{ title:'Gestiona tu suscripción', description:'Administra tu plan: actualízalo o cancélalo en cualquier momento.', side:"left", align:'center' }, onHighlighted: () => mostrarMenuUsuario(true), onDeselected: () => mostrarMenuUsuario(false) },
        { pagina:'estilo.php', element:'#inp_nombre', popover:{ title:'Nombra a tu asistente', description:'Define el nombre que identificará a tu chatbot.', side:"bottom", align:'start' } },
        { pagina:'estilo.php', element:'#urlLogotipo', popover:{ title:'Personaliza el logotipo', description:'Pega la URL de tu imagen para mostrar tu marca dentro del chat.', side:"top", align:'start' } },
        { pagina:'estilo.php', element:'#contenedor-colores-driver', popover:{ title:'Colores de marca', description:'Ajusta colores primarios, secundarios y de texto para tu identidad visual.', side:"right", align:'center' } },
        { pagina:'burbuja.php', element:'#inp_burbuja', popover:{ title:'Mensaje de la burbuja', description:'Define el mensaje que aparecerá en la burbuja del chat para invitar a los candidatos.', side:"bottom", align:'start' } },
        { pagina:'pantallaInicio.php', element:'#inp_saludo', popover:{ title:'Mensaje de bienvenida', description:'Configura el saludo que verán los candidatos al iniciar la conversación.', side:"right", align:'start' } },
        { pagina:'crearConversacion.php', element:'.container-pers3', popover:{ title:'Flujo de conversación', description:'Configura las opciones que guiarán a tus candidatos. Haz clic en cada una para editar sus detalles.', side:"bottom", align:'center' } },
        { pagina:'crearConversacion.php', element:'#boton1', popover:{ title:'Buscar vacantes por categoría', description:'Define el mensaje inicial, el origen de búsqueda (columna de categorías) y la URL del informe con vacantes activas.', side:"bottom", align:'center' }, onClick:'impMenu1' },
        { pagina:'crearConversacion.php', element:'#boton2', popover:{ title:'Buscar vacantes por ubicación', description:'Define el mensaje inicial y el origen de búsqueda (columna de ubicaciones).', side:"bottom", align:'center' }, onClick:'impMenu2' },
        { pagina:'crearConversacion.php', element:'#boton3', popover:{ title:'Seguimiento de postulación', description:'Define el mensaje inicial, el origen de búsqueda (correos de candidatos) y la URL del informe de estatus.', side:"bottom", align:'center' }, onClick:'impMenu3' },
        { pagina:'pantallaDespedida.php', element:'#inp_despedida', popover:{ title:'Mensaje de despedida', description:'El mensaje que verán los candidatos al finalizar la conversación.', side:"top", align:'start' } },
        { pagina:'finalizar.php', element:'.container-input', popover:{ title:'Dominio de implementación', description:'Especifica la URL donde funcionará tu chatbot. Si ya registraste una URL, contacta a soporte para modificarla.', side:"bottom", align:'center' } },
        { pagina:'finalizar.php', element:'#myBtn .btn-text-Generar', popover:{ title:'Generar código', description:'Obtén el código HTML para insertar IXAH en tu sitio web.', side:"top", align:'center' } },
        { pagina:'finalizar.php', element:'#btnInteractivo', popover:{ title:'Modo interactivo', description:'Previsualiza el funcionamiento y diseño de tu chatbot antes de publicarlo.', side:"top", align:'center' } },
        { pagina:'finalizar.php', popover:{ title: esPlus ? '¡Base lista! Ahora exploramos el Plan Plus' : '¡Todo listo!',
            description: esPlus ? 'Has completado la configuración base. A continuación te mostraremos las funcionalidades exclusivas de tu <b>Plan Plus</b>.' : 'Has completado el recorrido. ¡Manos a la obra!',
            side:"center", align:'center' } }
    ];

    //pasos exclusivos del plan plus
    const pasosPlus = [
        /* panel_admin_ia.php */
        { pagina:'panel_admin_ia.php', popover:{ title:'Panel de Configuración IA', description:'Sección <b>exclusiva del Plan Plus</b>. Configura el modelo de lenguaje, gestiona prompts y monitorea el consumo de tokens.', side:"center", align:'center' } },
        { pagina:'panel_admin_ia.php', element:'a[href="#model-config"]', popover:{ title:'Modelos IA', description:'Selecciona y configura el modelo de IA (ej. GPT-4). Con Plan Plus tienes <b>hasta 10 consultas diarias por usuario</b>.', side:"right", align:'center' } },
        { pagina:'panel_admin_ia.php', element:'a[href="#prompts"]', popover:{ title:'Gestión de Prompts', description:'Crea y edita las plantillas de instrucciones que definen cómo responde la IA en cada tipo de consulta.', side:"right", align:'center' } },
        { pagina:'panel_admin_ia.php', element:'a[href="#revisiones"]', popover:{ title:'Revisiones', description:'Consulta el historial de revisiones de la IA. Audita las respuestas generadas y detecta mejoras en tus prompts.', side:"right", align:'center' } },
        { pagina:'panel_admin_ia.php', element:'a[href="#tokens"]', popover:{ title:'Tokens en Panel IA', description:'Acceso rápido al estado de tokens desde el panel. También puedes consultarlos en la sección dedicada de Tokens.', side:"right", align:'center' } },

        /* vacantes.php */
        { pagina:'vacantes.php', popover:{ title:'Módulo de Vacantes', description:'Sección <b>exclusiva del Plan Plus</b>. Visualiza y gestiona todas las vacantes activas integradas con IXAH.', side:"center", align:'center' } },
        { pagina:'vacantes.php', element:'#tablaVacantes', popover:{ title:'Tabla de vacantes', description:'Consulta el listado de vacantes activas. Filtra por categoría o ubicación y accede a los candidatos postulados.', side:"top", align:'center' } },

        /* candidatos.php */
        { pagina:'candidatos.php', popover:{ title:'Módulo de Candidatos', description:'Sección <b>exclusiva del Plan Plus</b>. Revisa los candidatos postulados a tus vacantes y su estatus de postulación.', side:"center", align:'center' } },

        /* tokens.php */
        { pagina:'tokens.php', popover:{ title:'Consumo de Tokens', description:'Sección <b>exclusiva del Plan Plus</b>. Monitorea el consumo acumulado de tokens de tu cuenta y optimiza el uso de las consultas de IA.', side:"center", align:'center' } },

        /* log_errores.php */
        { pagina:'log_errores.php', popover:{ title:'Errores de los ChatBots', description:'Sección <b>exclusiva del Plan Plus</b>. Aquí puedes revisar los errores registrados de la API para detectar y corregir problemas en tu chatbot.', side:"center", align:'center' } },

        /* cierre */
        { pagina:'log_errores.php', popover:{ title:'¡Todo listo con tu Plan Plus!', description:'Has completado el recorrido completo. Ya conoces todas tus funcionalidades exclusivas: <b>IA, vacantes, candidatos, tokens y monitoreo de errores</b>. ¡Manos a la obra!', side:"center", align:'center' } }
    ];
    const paginasBase = ['menu.php','estilo.php','burbuja.php','pantallaInicio.php','crearConversacion.php','pantallaDespedida.php','finalizar.php'];
    const paginasPlus = ['panel_admin_ia.php','vacantes.php','candidatos.php','tokens.php','log_errores.php'];
    const todasLasPaginas = [...paginasBase, ...paginasPlus];

    let paginaActual = null;
    for (const p of todasLasPaginas) {
        if (path.includes(p)) { paginaActual = p; break; }
    }
    if (!paginaActual && (path === '/' || path.endsWith('/'))) paginaActual = 'menu.php';
    if (!paginaActual) return;

    const enPaginaBase = paginasBase.includes(paginaActual);
    const enPaginaPlus = paginasPlus.includes(paginaActual);

    const ejecutarBase = debeEjecutarBase && enPaginaBase;
    const ejecutarPlus = debeEjecutarPlus && enPaginaPlus;

    // Si el usuario está en página Plus pero el tour plus no está activo y el base no está visto, salir
    if (enPaginaPlus && !plusActivo && !baseVisto) return;
    if (!ejecutarBase && !ejecutarPlus) return;

    const todosLosPasos = ejecutarPlus ? pasosPlus : pasosBase;
    const prefijo       = ejecutarPlus ? 'ixah_plus' : 'ixah';

   //navegación entre páginas del tour
    const navMapBase = {
        'menu.php':              (id) => id ? `estilo.php?id_chatbot=${id}` : 'estilo.php?nuevo=1',
        'estilo.php':            () => 'burbuja.php',
        'burbuja.php':           () => 'pantallaInicio.php',
        'pantallaInicio.php':    () => 'crearConversacion.php',
        'crearConversacion.php': () => 'pantallaDespedida.php',
        'pantallaDespedida.php': () => 'finalizar.php',
        'finalizar.php':         () => null  // transición a Plus en onNextClick
    };
    const navMapPlus = {
        'panel_admin_ia.php': () => 'vacantes.php',
        'vacantes.php':       () => 'candidatos.php',
        'candidatos.php':     () => 'tokens.php',
        'tokens.php':         () => 'log_errores.php',
        'log_errores.php':    () => null
    };
    const navMapBaseRev = {
        'estilo.php':            () => 'menu.php',
        'burbuja.php':           () => 'estilo.php',
        'pantallaInicio.php':    () => 'burbuja.php',
        'crearConversacion.php': () => 'pantallaInicio.php',
        'pantallaDespedida.php': () => 'crearConversacion.php',
        'finalizar.php':         () => 'pantallaDespedida.php'
    };
    const navMapPlusRev = {
        'panel_admin_ia.php': () => 'finalizar.php',
        'vacantes.php':       () => 'panel_admin_ia.php',
        'candidatos.php':     () => 'vacantes.php',
        'tokens.php':         () => 'candidatos.php',
        'log_errores.php':    () => 'tokens.php'
    };

    const navMap        = ejecutarPlus ? navMapPlus        : navMapBase;
    const navMapReverse = ejecutarPlus ? navMapPlusRev     : navMapBaseRev;

    const indiceActual = parseInt(localStorage.getItem(`${prefijo}_tour_step`) || '0');
    const pasosEstaPagina = todosLosPasos
        .map((paso, idx) => ({ ...paso, indiceGlobal: idx }))
        .filter(paso => paso.pagina === paginaActual && paso.indiceGlobal >= indiceActual);

    if (pasosEstaPagina.length === 0) return;

    const elementosMenu = ['#user-btn',"a[href*='freshdesk']",'#sftpLink',"a[href*='billing.stripe.com']"];
    if (paginaActual === 'menu.php' && elementosMenu.includes(pasosEstaPagina[0]?.element)) mostrarMenuUsuario(true);
    if (paginaActual === 'crearConversacion.php' && pasosEstaPagina[0]?.onClick) {
        const fn = window[pasosEstaPagina[0].onClick];
        if (typeof fn === 'function') fn(new Event('click'));
    }

    if (paginaActual === 'finalizar.php' && typeof $ !== 'undefined') {
        $('#myModal').on('show.bs.modal', function (e) {
            if (localStorage.getItem(`${prefijo}_tour_activo`) === 'true') { e.preventDefault(); e.stopPropagation(); return false; }
        });
    }

    const stepsDriver = pasosEstaPagina.map(paso => {
        const step = { popover: paso.popover };
        if (paso.element) {
            step.element = paso.element;
            if (paso.onClick) step.onHighlighted = () => { const fn = window[paso.onClick]; if (typeof fn === 'function') fn(new Event('click')); };
        }
        if (paso.onHighlighted) step.onHighlighted = paso.onHighlighted;
        if (paso.onDeselected)  step.onDeselected  = paso.onDeselected;
        return step;
    });

    const stepsValidos = stepsDriver.filter(step => {
        if (!step.element) return true;
        const existe = document.querySelector(step.element);
        if (!existe) console.warn('[IXAH Tour] Elemento no encontrado:', step.element);
        return existe !== null;
    });

    if (stepsValidos.length === 0) {
        // Si ningún paso tiene elemento válido en esta página, navegar a la siguiente
        const dest = navMap[paginaActual];
        if (dest) { const url = dest(chatbotId); if (url) window.location.href = url; }
        return;
    }

    const ultimoIndice = pasosEstaPagina[pasosEstaPagina.length - 1].indiceGlobal;
    const esUltimoPaso = ultimoIndice === todosLosPasos.length - 1;

//driver.js configurción y ejecución
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
            mostrarMenuUsuario(false);
            const btn = document.getElementById("btnGuiaFlotante");
            if (btn) btn.style.display = "block";
            driverObj.destroy();
        },

        onNextClick: () => {
            const idx        = driverObj.getActiveIndex();
            const pasoActual = pasosEstaPagina[idx];

            localStorage.setItem(`${prefijo}_tour_step`, pasoActual.indiceGlobal + 1);
            if (chatbotId) localStorage.setItem('ixah_chatbot_id', chatbotId);

            if (idx < stepsValidos.length - 1) {
                driverObj.moveNext();
            } else {
                const siguientePaso = todosLosPasos[pasoActual.indiceGlobal + 1];
                const dest = navMap[paginaActual];
                const url  = dest ? dest(localStorage.getItem('ixah_chatbot_id') || chatbotId) : null;

                driverObj.destroy();

                if (url) {
                    window.location.href = url;
                } else if (ejecutarBase && esPlus) {
                    // Transición base a plus
                    localStorage.setItem('ixah_tour_visto', 'true');
                    localStorage.setItem('ixah_tour_activo', 'false');
                    localStorage.setItem('ixah_plus_tour_activo', 'true');
                    localStorage.setItem('ixah_plus_tour_visto', 'false');
                    localStorage.removeItem('ixah_plus_tour_step');
                    setTimeout(() => { window.location.href = 'panel_admin_ia.php'; }, 300);
                } else {
                    // Tour completado (base sin plus, o plus terminado)
                    localStorage.setItem(`${prefijo}_tour_activo`, 'false');
                    localStorage.setItem(`${prefijo}_tour_visto`, 'true');
                    localStorage.removeItem(`${prefijo}_tour_step`);
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

//Relanzar tour desde botón ? — siempre desde el inicio completo
function activarRecorrido() {
    const btn = document.getElementById("btnGuiaFlotante");
    if (btn) btn.style.display = "none";

    const plan   = localStorage.getItem('ixah_plan_usuario') || 'basico';
    const esPlus = ['plusMensual','plusAnual'].includes(plan);
    const path   = window.location.pathname;

    const paginasBase = ['menu.php','estilo.php','burbuja.php','pantallaInicio.php',
                         'crearConversacion.php','pantallaDespedida.php','finalizar.php'];
    const paginasPlus = ['panel_admin_ia.php','vacantes.php','candidatos.php',
                         'tokens.php','log_errores.php'];

    // Detectar página actual
    let paginaActual = 'menu.php';
    for (const p of [...paginasBase, ...paginasPlus]) {
        if (path.includes(p)) { paginaActual = p; break; }
    }

    // ── LIMPIAR TODO el estado residual de tours anteriores ──
    localStorage.removeItem('ixah_tour_activo');
    localStorage.removeItem('ixah_tour_visto');
    localStorage.removeItem('ixah_tour_step');
    localStorage.removeItem('ixah_chatbot_id');
    localStorage.removeItem('ixah_plus_tour_activo');
    localStorage.removeItem('ixah_plus_tour_visto');
    localStorage.removeItem('ixah_plus_tour_step');

    const enPaginaPlus = paginasPlus.includes(paginaActual);

    if (enPaginaPlus && esPlus) {
        // ── Página Plus → relanzar tour Plus desde esta página ──
        const indicesPorPagina = {
            'panel_admin_ia.php': 0,
            'vacantes.php':       5,
            'candidatos.php':     7,
            'tokens.php':         8,
            'log_errores.php':    9
        };
        localStorage.setItem('ixah_tour_visto', 'true');
        localStorage.setItem('ixah_plus_tour_activo', 'true');
        localStorage.setItem('ixah_plus_tour_visto', 'false');
        localStorage.setItem('ixah_plus_tour_step', indicesPorPagina[paginaActual] ?? 0);
        window.location.reload();

    } else {
        // ── Página base → relanzar tour base desde esta página ──
        const indicesPorPagina = {
            'menu.php':              0,
            'estilo.php':            6,
            'burbuja.php':           9,
            'pantallaInicio.php':    10,
            'crearConversacion.php': 11,
            'pantallaDespedida.php': 15,
            'finalizar.php':         16
        };
        localStorage.setItem('ixah_tour_activo', 'true');
        localStorage.setItem('ixah_tour_visto', 'false');
        localStorage.setItem('ixah_tour_step', indicesPorPagina[paginaActual] ?? 0);
        if (esPlus) {
            localStorage.setItem('ixah_plus_tour_visto', 'false');
        }
        window.location.reload();
    }
}
window.activarRecorrido = activarRecorrido;
