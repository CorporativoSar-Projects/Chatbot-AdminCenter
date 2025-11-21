// js/IA/analisisIA.js

const API_BASE = "http://localhost:8000/api";

// Función para mostrar/ocultar botones de selección en la tabla
function toggleSelectionButtons(show) {
    const buttons = document.querySelectorAll('.btn-select-candidate');
    buttons.forEach(button => {
        button.style.display = show ? 'block' : 'none';
    });
    // Ocultar botones de mejora cuando se muestran los de análisis
    if (show) {
        toggleImproveButtons(false);
    }
}

// Función para seleccionar candidato desde la tabla
function seleccionarParaAnalisis(id) {
    const candidato = candidatosData.find(c => c.id_candidate == id);
    if (candidato) {
        // Ocultar todos los botones de selección
        toggleSelectionButtons(false);
        
        // Mostrar mensaje en el chat
        addMessage(`Seleccioné a ${candidato.nombre_candidate} ${candidato.apellidop_candidate} para análisis`, 'user-message');
        
        // Iniciar función de análisis
        iniciarAnalisisCandidato(candidato);
    }
}

// Función que se ejecuta cuando se selecciona un candidato
function iniciarAnalisisCandidato(candidato) {
    console.log('Candidato seleccionado para análisis:', candidato);
    
    // Mostrar análisis básico en el chat
    const analysisHTML = `
        <div class="candidate-analysis">
            <h5>🔍 Análisis de ${candidato.nombre_candidate} ${candidato.apellidop_candidate}</h5>
            <div class="analysis-field">
                <strong>📧 Email:</strong> ${candidato.correo_candidate}
            </div>
            <div class="analysis-field">
                <strong>💼 Puesto aplicado:</strong> ${candidato.puesto || 'No especificado'}
            </div>
            <div class="analysis-field">
                <strong>📊 Estado del proceso:</strong> 
                <span class="badge badge-${candidato.estado === 'Contratado' ? 'success' : 'warning'}">
                    ${candidato.estado || 'Pendiente'}
                </span>
            </div>
            <div class="analysis-field">
                <strong>📞 Teléfono:</strong> ${candidato.tel_candidate}
            </div>
            <div class="analysis-field">
                <strong>🎯 Análisis IA:</strong> Preparando evaluación detallada...
            </div>
        </div>
    `;
    
    addHTMLMessage(analysisHTML, 'bot-message');
    
    // Mostrar confirmación después del análisis
    setTimeout(() => {
        mostrarConfirmacionAyuda();
    }, 1000);
}

// Función modificada para mostrar análisis de candidatos
function mostrarAnalisisCandidato() {
    addMessage('Quiero analizar un candidato', 'user-message');
    
    // Ocultar el input de búsqueda por nombre
    document.getElementById('analysis-input-container').style.display = 'none';
    
    // Mostrar botones de selección en la tabla
    toggleSelectionButtons(true);
    
    // Mensaje instructivo
    addMessage('Por favor, selecciona un candidato de la tabla haciendo clic en "✅ Seleccionar para Análisis"', 'bot-message');
}

// Función para análisis con IA (placeholder para futura implementación)
function analizarCandidatoConIA(candidato) {
    // Esta función se conectará con la API de IA
    console.log('Analizando candidato con IA:', candidato);
    
    // Ejemplo de llamada a API (descomentar cuando esté lista)
    /*
    fetch('/api/analizar-candidato', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            candidato: candidato,
            datos_adicionales: {} // Agregar más datos si es necesario
        })
    })
    .then(response => response.json())
    .then(data => {
        // Procesar respuesta de la IA
        mostrarResultadoIA(data);
    })
    .catch(error => {
        console.error('Error en análisis IA:', error);
    });
    */
}

// Función para mostrar resultados de IA
function mostrarResultadoIA(resultado) {
    const resultadoHTML = `
        <div class="candidate-analysis" style="border-left-color: #28a745;">
            <h5>🤖 Análisis IA Completo</h5>
            <div class="analysis-field">
                <strong>📊 Puntuación general:</strong> ${resultado.puntuacion || 'N/A'}
            </div>
            <div class="analysis-field">
                <strong>💡 Fortalezas:</strong> ${resultado.fortalezas || 'Por analizar'}
            </div>
            <div class="analysis-field">
                <strong>⚠️ Áreas de mejora:</strong> ${resultado.areas_mejora || 'Por analizar'}
            </div>
            <div class="analysis-field">
                <strong>🎯 Recomendación:</strong> ${resultado.recomendacion || 'Por analizar'}
            </div>
        </div>
    `;
    
    addHTMLMessage(resultadoHTML, 'bot-message');
    
    // Mostrar confirmación después del análisis
    setTimeout(() => {
        mostrarConfirmacionAyuda();
    }, 1000);
}

// Función para mejorar descripciones de puestos
/*
function mejorarDescripcionPuesto() {
    addMessage('Quiero mejorar una descripción de puesto', 'user-message');
    
    // Ocultar cualquier input de análisis de candidatos
    document.getElementById('analysis-input-container').style.display = 'none';
    toggleSelectionButtons(false);
    
    setTimeout(() => {
        addHTMLMessage(`
            <div class="candidate-analysis">
                <h5>✏️ Mejorar Descripciones de Puestos</h5>
                <p>Esta funcionalidad te permitirá:</p>
                <ul>
                    <li>Optimizar descripciones de vacantes</li>
                    <li>Mejorar atractivo para candidatos</li>
                    <li>Incluir palabras clave relevantes</li>
                </ul>
                <p><strong>Próximamente disponible</strong></p>
            </div>
        `, 'bot-message');
        
        // Mostrar confirmación después del mensaje
        setTimeout(() => {
            mostrarConfirmacionAyuda();
        }, 500);
    }, 1000);
}*/

// Función para mostrar confirmación de ayuda
function mostrarConfirmacionAyuda() {
    const confirmacionHTML = `
        <div class="chat-message bot-message">
            <p>¿Puedo ayudarte con algo más?</p>
            <div class="special-buttons" style="margin-top: 10px;">
                <button class="special-btn" onclick="manejarConfirmacion(true)" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    Sí
                </button>
                <button class="special-btn" onclick="manejarConfirmacion(false)" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                    No
                </button>
            </div>
        </div>
    `;
    
    addHTMLMessage(confirmacionHTML, 'bot-message');
}

// Función para manejar la respuesta de confirmación
function manejarConfirmacion(respuesta) {
    if (respuesta) {
        // Si dice "Sí", mostrar las opciones principales
        addMessage('Sí', 'user-message');
        mostrarOpcionesPrincipales();
    } else {
        // Si dice "No", mostrar mensaje de espera
        addMessage('No', 'user-message');
        mostrarMensajeEspera();
    }
}

// Función para mostrar las opciones principales
function mostrarOpcionesPrincipales() {
    // document.getElementById('manual-description-container').style.display = 'none';
    document.getElementById('analysis-input-container').style.display = 'none';
    toggleSelectionButtons(false);
    toggleImproveButtons(false);

    const opcionesHTML = `
        <div class="special-buttons">
            <p>¡Perfecto! ¿En qué más puedo ayudarte?</p>
            <div class="special-buttons">
                <button class="special-btn" onclick="mostrarAnalisisCandidato()">
                    🔍 Análisis de candidatos
                </button>
                <button class="special-btn" onclick="mejorarDescripcionPuesto()">
                    ✏️ Mejorar descripciones de puestos
                </button>
                <button class="special-btn" onclick="mostrarInputManualDescripcion()">
                        ✏️ Mejorar descripción manual
                </button>
            </div>
        </div>
    `;
    
    addHTMLMessage(opcionesHTML, 'bot-message');
}

// Función para mostrar mensaje de espera
function mostrarMensajeEspera() {
    const esperaHTML = `
        <div class="chat-message bot-message">
            <p>¡Claro! Estoy aquí pendiente por si necesitas algo.</p>
            <div class="special-buttons" style="margin-top: 10px;">
                <button class="special-btn" onclick="mostrarOpcionesPrincipales()" style="background: linear-gradient(135deg, #002B45 0%, #3ca6e5 100%);">
                    Ahora sí, necesito ayuda
                </button>
            </div>
        </div>
    `;
    
    addHTMLMessage(esperaHTML, 'bot-message');
}

// Función auxiliar para agregar mensajes HTML (si no está definida en el main)
function addHTMLMessage(html, type) {
    const chatBody = document.getElementById('chat-body');
    const div = document.createElement('div');
    div.classList.add('chat-message', type);
    div.innerHTML = html;
    chatBody.appendChild(div);
    chatBody.scrollTop = chatBody.scrollHeight;
}

// Función auxiliar para agregar mensajes de texto (si no está definida en el main)
function addMessage(message, type) {
    const chatBody = document.getElementById('chat-body');
    const div = document.createElement('div');
    div.classList.add('chat-message', type);
    div.textContent = message;
    chatBody.appendChild(div);
    chatBody.scrollTop = chatBody.scrollHeight;
}

// NUEVO

// Función para mostrar/ocultar botones de mejora de puestos
function toggleImproveButtons(show) {
    const buttons = document.querySelectorAll('.btn-improve-job');
    buttons.forEach(button => {
        button.style.display = show ? 'block' : 'none';
    });
    
    // Ocultar botones de análisis cuando se muestran los de mejora
    if (show) {
        toggleSelectionButtons(false);
    }
}

// Función para seleccionar candidato para mejora de puesto
function seleccionarParaMejoraPuesto(id) {
    const candidato = candidatosData.find(c => c.id_candidate == id);
    if (candidato) {
        // Ocultar todos los botones de mejora
        toggleImproveButtons(false);
        
        // Mostrar mensaje en el chat
        addMessage(`Seleccioné a ${candidato.nombre_candidate} ${candidato.apellidop_candidate} para mejorar la descripción del puesto: ${candidato.puesto || 'Sin especificar'}`, 'user-message');
        
        // Iniciar función de mejora de descripción
        iniciarMejoraDescripcionPuesto(candidato);
    }
}

// FUNCIÓN ACTUALIZADA - Conectada a tu API real
async function iniciarMejoraDescripcionPuesto(candidato) {
    console.log('Candidato seleccionado para mejora de puesto:', candidato);
    
    // Mostrar información del puesto en el chat
    const mejoraHTML = `
        <div class="candidate-analysis">
            <h5>✏️ Mejorar Descripción de Puesto</h5>
            <div class="analysis-field">
                <strong>👤 Candidato:</strong> ${candidato.nombre_candidate} ${candidato.apellidop_candidate}
            </div>
            <div class="analysis-field">
                <strong>💼 Puesto actual:</strong> ${candidato.puesto || 'No especificado'}
            </div>
            <div class="analysis-field">
                <strong>📧 Email:</strong> ${candidato.correo_candidate}
            </div>
            <div class="analysis-field">
                <strong>🔄 Proceso:</strong> Consultando con IA para generar descripción mejorada...
            </div>
        </div>
    `;
    
    addHTMLMessage(mejoraHTML, 'bot-message');
    
    try {
        // LLAMADA REAL A TU API DE DJANGO
        const response = await fetch(`${API_BASE}/mejorar-descripcion/`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                candidato: {
                    nombre: `${candidato.nombre_candidate} ${candidato.apellidop_candidate}`,
                    email: candidato.correo_candidate,
                    puesto: candidato.puesto
                },
                puesto_actual: candidato.puesto || 'Puesto no especificado'
            })
        });

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.error) {
            throw new Error(data.error);
        }
        // Mostrar la respuesta real de la IA
        mostrarDescripcionMejoradaReal(candidato, data.descripcion_mejorada);
        
    } catch (error) {
        console.error('Error al conectar con la API:', error);
        // Fallback a versión simulada si falla la API
        addMessage('⚠️ El servicio de IA no está disponible en este momento. Por favor intenta más tarde.', 'bot-message');
        /*
        setTimeout(() => {
            mostrarDescripcionMejorada(candidato);
        }, 1000);*/
         setTimeout(() => {
            mostrarConfirmacionAyuda();
        }, 500);
    }
}

// FUNCIÓN ACTUALIZADA - Para mostrar respuesta real de la IA
function mostrarDescripcionMejoradaReal(candidato, descripcionIA) {
    const descripcionMejoradaHTML = `
        <div class="candidate-analysis" style="border-left: 4px solid #ff6b35;">
            <h5>🚀 Descripción Mejorada por IA para: ${candidato.puesto}</h5>
            <div class="analysis-field">
                <strong>🤖 Análisis IA:</strong>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin-top: 10px; white-space: pre-wrap; font-size: 14px; line-height: 1.5;">
                    ${descripcionIA}
                </div>
            </div>
        </div>
        
        <div class="special-buttons" style="margin-top: 15px;">
            <button class="special-btn" onclick="copiarDescripcionIA('${descripcionIA.replace(/'/g, "\\'").replace(/\n/g, '\\n')}')" style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);">
                📋 Copiar Descripción
            </button>
            <button class="special-btn" onclick="personalizarDescripcion(${candidato.id_candidate})" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                🎨 Personalizar Más
            </button>
        </div>
    `;
    
    addHTMLMessage(descripcionMejoradaHTML, 'bot-message');
    
    // Mostrar confirmación después de la mejora
    setTimeout(() => {
        mostrarConfirmacionAyuda();
    }, 1000);
}

// Función para copiar descripción IA real
function copiarDescripcionIA(descripcion) {
    const descripcionTexto = descripcion.replace(/\\n/g, '\n').replace(/\\'/g, "'");
    
    navigator.clipboard.writeText(descripcionTexto).then(() => {
        addMessage('✅ Descripción copiada al portapapeles', 'bot-message');
    }).catch(err => {
        console.error('Error al copiar: ', err);
        addMessage('❌ Error al copiar la descripción', 'bot-message');
    });
}

// Función para personalizar más la descripción
function personalizarDescripcion(idCandidato) {
    const candidato = candidatosData.find(c => c.id_candidate == idCandidato);
    addMessage(`Quiero personalizar más la descripción para ${candidato.nombre_candidate}`, 'user-message');
    
    setTimeout(() => {
        addHTMLMessage(`
            <div class="chat-message bot-message">
                <p>Para personalizar la descripción de ${candidato.puesto}, por favor especifica:</p>
                <ul>
                    <li>🔹 Nivel de experiencia requerido</li>
                    <li>🔹 Habilidades técnicas específicas</li>
                    <li>🔹 Beneficios adicionales</li>
                    <li>🔹 Modalidad de trabajo (presencial/remoto)</li>
                </ul>
                <p>Puedes escribir tus requerimientos en el chat.</p>
            </div>
        `, 'bot-message');
    }, 1000);
}

// MODIFICAR la función mejorarDescripcionPuesto existente:
function mejorarDescripcionPuesto() {
    addMessage('Quiero mejorar una descripción de puesto', 'user-message');
    
    // Ocultar input de análisis de candidatos
    document.getElementById('analysis-input-container').style.display = 'none';
    toggleSelectionButtons(false);
    
    // Mostrar botones de mejora en la tabla
    toggleImproveButtons(true);
    
    // Mensaje instructivo
    addMessage('Por favor, selecciona un candidato de la tabla haciendo clic en "✏️ Mejorar Descripción" para mejorar el puesto al que aplicó', 'bot-message');
}

// FUNCIÓN DE FALLBACK (simulada - se usa si la API falla)
function mostrarDescripcionMejorada(candidato) {
    const puesto = candidato.puesto || 'Puesto no especificado';
    
    const descripcionMejoradaHTML = `
        <div class="candidate-analysis" style="border-left: 4px solid #ff6b35;">
            <h5>🚀 Descripción Mejorada para: ${puesto}</h5>
            
            <div class="analysis-field">
                <strong>📋 Título Optimizado:</strong> Especialista en ${puesto} - Oportunidad de Crecimiento
            </div>
            
            <div class="analysis-field">
                <strong>🎯 Responsabilidades Clave:</strong>
                <ul style="margin: 5px 0; padding-left: 20px;">
                    <li>Gestión y coordinación de actividades del área</li>
                    <li>Desarrollo e implementación de estrategias</li>
                    <li>Supervisión de procesos y mejora continua</li>
                    <li>Colaboración interdepartamental</li>
                </ul>
            </div>
            
            <div class="analysis-field">
                <strong>✅ Requisitos Deseables:</strong>
                <ul style="margin: 5px 0; padding-left: 20px;">
                    <li>Experiencia comprobada en puesto similar</li>
                    <li>Habilidades de liderazgo y comunicación</li>
                    <li>Capacidad analítica y resolutiva</li>
                    <li>Orientación a resultados</li>
                </ul>
            </div>
            
            <div class="analysis-field">
                <strong>🌟 Beneficios Destacados:</strong>
                <ul style="margin: 5px 0; padding-left: 20px;">
                    <li>Plan de desarrollo profesional</li>
                    <li>Ambiente de trabajo colaborativo</li>
                    <li>Oportunidades de crecimiento</li>
                    <li>Paquete de beneficios competitivo</li>
                </ul>
            </div>
        </div>
        
        <div class="special-buttons" style="margin-top: 15px;">
            <button class="special-btn" onclick="copiarDescripcion('${puesto}')" style="background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);">
                📋 Copiar Descripción
            </button>
            <button class="special-btn" onclick="personalizarDescripcion(${candidato.id_candidate})" style="background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);">
                🎨 Personalizar Más
            </button>
        </div>
    `;
    
    addHTMLMessage(descripcionMejoradaHTML, 'bot-message');
}

// Función para copiar descripción simulada
function copiarDescripcion(puesto) {
    const descripcion = `Descripción mejorada para: ${puesto}\n\nTítulo: Especialista en ${puesto} - Oportunidad de Crecimiento\n\nResponsabilidades:\n• Gestión y coordinación de actividades\n• Desarrollo de estrategias\n• Supervisión de procesos\n• Colaboración interdepartamental\n\nRequisitos:\n• Experiencia en puesto similar\n• Habilidades de liderazgo\n• Capacidad analítica\n• Orientación a resultados`;
    
    navigator.clipboard.writeText(descripcion).then(() => {
        addMessage('✅ Descripción copiada al portapapeles', 'bot-message');
    }).catch(err => {
        console.error('Error al copiar: ', err);
        addMessage('❌ Error al copiar la descripción', 'bot-message');
    });
}

// Función para personalizar más la descripción
function personalizarDescripcion(idCandidato) {
    const candidato = candidatosData.find(c => c.id_candidate == idCandidato);
    addMessage(`Quiero personalizar más la descripción para ${candidato.nombre_candidate}`, 'user-message');
    
    setTimeout(() => {
        addHTMLMessage(`
            <div class="chat-message bot-message">
                <p>Para personalizar la descripción de "${candidato.puesto}", por favor especifica en el chat:</p>
                <ul>
                    <li>🔹 Nivel de experiencia requerido</li>
                    <li>🔹 Habilidades técnicas específicas</li>
                    <li>🔹 Beneficios adicionales</li>
                    <li>🔹 Modalidad de trabajo (presencial/remoto)</li>
                    <li>🔹 Cualquier otro requerimiento especial</li>
                </ul>
                <p>Escribe tus especificaciones y las enviaré a la IA para una personalización avanzada.</p>
            </div>
        `, 'bot-message');
    }, 1000);
}


async function enviarDescripcionParaMejora() {
    const descripcion = document.getElementById('descripcion-puesto-input').value.trim();

    if (!descripcion) {
        Swal.fire('⚠️', 'Por favor ingresa la descripción del puesto antes de enviarla.', 'warning');
        return;
    }

    // Mostrar mensaje de proceso
    addHTMLMessage(`
        <div class="chat-message bot-message">
            🔄 Enviando descripción a IA para mejorar...
        </div>
    `, 'bot-message');

    try {
        const response = await fetch(`${API_BASE}/mejorar-descripcion/`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                candidato: { nombre: 'Manual Input', email: '', puesto: descripcion },
                puesto_actual: descripcion
            })
        });

        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);

        const data = await response.json();

        if (data.error) throw new Error(data.error);

        // Mostrar resultado optimizado por IA
        addHTMLMessage(`
            <div class="chat-message bot-message">
                ✅ Descripción mejorada por IA:<br>
                ${data.descripcion_mejorada.replace(/\n/g, '<br>')}
            </div>
        `, 'bot-message');
        setTimeout(() => {
            mostrarConfirmacionAyuda();
        }, 500);

    } catch (error) {
        console.error('Error al conectar con la API:', error);
        Swal.fire('⚠️', 'La IA no está disponible en este momento. Intenta más tarde.', 'error');
        // DETALLES DEL MENSAJE DE ADVERTENCIA SOLO AQUI
        // addMessage('⚠️ El servicio de IA no está disponible en este momento. Por favor intenta más tarde.', 'bot-message');
        /*
        setTimeout(() => {
            mostrarDescripcionMejorada(candidato);
        }, 1000);*/
        setTimeout(() => {
            mostrarConfirmacionAyuda();
        }, 500);
    }
}

function mostrarInputManualDescripcion() {
    // Ocultar inputs de análisis y botones de mejora de candidatos
    document.getElementById('analysis-input-container').style.display = 'none';
    toggleSelectionButtons(false);
    toggleImproveButtons(false);

    // Agregar el textarea y el botón **dentro del chat** cada vez
    const html = `
        
            <p>✏️ Ingresa la descripción del puesto que deseas mejorar:</p>
            <textarea id="descripcion-puesto-input" class="analysis-input" rows="4" placeholder="Escribe aquí la descripción del puesto que deseas mejorar..."></textarea>
            <button class="analysis-btn" onclick="enviarDescripcionParaMejora()">
                ✏️ Optimizar descripción con IA
            </button>
        
    `;
    addHTMLMessage(html, 'bot-message');
}