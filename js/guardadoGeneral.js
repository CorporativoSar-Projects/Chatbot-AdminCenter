function validarURL(valor) {
    if (!valor) return true; // Permite vacío si no es obligatorio
    try {
        const url = new URL(valor);
        return url.protocol === "http:" || url.protocol === "https:";
    } catch (_) {
        return false;
    }
}

function guardarChatbotCompleto() {
    // Obtener URLs del localStorage
    const url1 = localStorage.getItem("inp_url_informe") || "";
    const url3 = localStorage.getItem("inp_url_informe3") || "";

    // Validar URLs antes de guardar
    if (url1 && !validarURL(url1)) {
        Swal.fire({
            icon: "error",
            title: "URL inválida",
            text: "La URL del informe del tema 1 no es válida. Debe comenzar con http o https.",
            confirmButtonColor: "#ffb703"
        });
        return;
    }

    if (url3 && !validarURL(url3)) {
        Swal.fire({
            icon: "error",
            title: "URL inválida",
            text: "La URL del informe del tema 3 no es válida. Debe comenzar con http o https.",
            confirmButtonColor: "#ffb703"
        });
        return;
    }
    // ==========================
    // NORMALIZAR URL DE IMAGEN
    // ==========================
    function normalizarURLImagen(url) {
        if (!url) return "";
        url = url.trim();
        // Si NO empieza con http:// o https:// se agrega https://
        if (!/^https?:\/\//i.test(url)) {
            url = "https://" + url;
        }
        return url;
    }

    // ==========================
    // URL por defecto
    // ==========================
    const URL_DEFAULT_LOGO = "https://ixahcenter.giintapeinnovahue.com/img/Logo_cabeza.svg"; // <- Cambia por la URL real

    // Manejar logo: mantener el anterior si no hay nuevo input, fallback a IXAH
    const urlLogotipoInput = document.getElementById("urlLogotipo")?.value.trim() || "";
    const logoPrevio = localStorage.getItem("chatbotLogo") || "";
    let logoURL = "";

    if (urlLogotipoInput !== "") {
        logoURL = normalizarURLImagen(urlLogotipoInput);
    } else if (logoPrevio !== "") {
        logoURL = normalizarURLImagen(logoPrevio);
    } else {
        logoURL = URL_DEFAULT_LOGO;
    }

    // Guardar la URL del logo en localStorage
    localStorage.setItem("chatbotLogo", logoURL);

    //  Preparar datos para envío
    const datosPlanos = {
        id_chatbot: localStorage.getItem("id_chatbot") || null,
        inp_nombre: localStorage.getItem("inp_nombre") ?? null,
        colorPrimario: localStorage.getItem("colorPrimario") || "#3ca6e5",
        colorSecundario: localStorage.getItem("colorSecundario") || "#b6b6b6",
        colorTexto: localStorage.getItem("colorTexto") || "#000000",
        colorAcento: localStorage.getItem("colorAcento") || "#383838",
        colorRespuestaUsuario: localStorage.getItem("colorRespuestaUsuario") || "#219ebc",
        urlLogotipo: logoURL,
        inp_burbuja: localStorage.getItem("inp_burbuja") || "",
        inp_saludo: localStorage.getItem("inp_saludo") || "",
        inp_conversa1: "Buscar vacantes por categoría",
        inp_conversa2: "Buscar vacantes por ubicación",
        inp_conversa3: "Seguimiento de mi postulación",
        inp_mensaje_usuario: localStorage.getItem("inp_mensaje_usuario") || "",
        inp_columna: "category_ix",
        inp_url_informe: url1,
        inp_mensaje_usuario2: localStorage.getItem("inp_mensaje_usuario2") || "",
        inp_columna2: "location_ix",
        inp_mensaje_usuario3: localStorage.getItem("inp_mensaje_usuario3") || "",
        inp_columna3: "email_ix",
        inp_url_informe3: url3,
        inp_despedida: localStorage.getItem("inp_despedida") || ""
    };

    // 🚀 Enviar al backend
    fetch("modelo/guardadoTotal.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(datosPlanos)
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (data.id_chatbot) {
                    localStorage.setItem("id_chatbot", data.id_chatbot);
                }

                if (data.message.includes("creado")) {
                    Swal.fire({
                        icon: "success",
                        title: "¡Chatbot creado!",
                        text: "Tu chatbot se guardó exitosamente.",
                        confirmButtonColor: "#ffb703"
                    });
                } else if (data.message.includes("actualizado")) {
                    Swal.fire({
                        icon: "info",
                        title: "Chatbot actualizado",
                        text: "Los cambios en tu chatbot se guardaron correctamente.",
                        confirmButtonColor: "#ffb703"
                    });
                } else {
                    Swal.fire({
                        icon: "success",
                        title: "Guardado exitoso",
                        text: data.message || "Cambios aplicados correctamente.",
                        confirmButtonColor: "#ffb703"
                    });
                }
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error al guardar",
                    text: data.error || "Ocurrió un error desconocido."
                });
            }
        })
        .catch(err => {
            console.error("Error en guardado general:", err);
            Swal.fire({
                icon: "error",
                title: "Error de red",
                text: "No se pudo conectar con el servidor."
            });
        });
}