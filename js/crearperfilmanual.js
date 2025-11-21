// crear_perfil_manual.js

const API_BASE = window.API_BASE || 'http://localhost:8000/'; // Base URL de tu API

// Función principal para crear perfil manual
async function crearPerfilManual(descripcion) {
    try {
        const formData = new FormData();
        formData.append('descripcion', descripcion);

        const response = await fetch(`${API_BASE}perfiles/crear/`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Error del servidor');
        }

        return data;
    } catch (error) {
        console.error('Error creando perfil:', error);
        throw error;
    }
}

// Función para cargar la lista de perfiles
async function cargarPerfiles() {
    try {
        const response = await fetch(`${API_BASE}perfiles/listar/`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('Error al cargar perfiles');
        }

        const data = await response.json();
        return data.perfiles || [];
    } catch (error) {
        console.error('Error cargando perfiles:', error);
        throw error;
    }
}

// Función para mostrar perfiles en una tabla
function mostrarPerfiles(perfiles) {
    const tablaBody = document.getElementById('tablaPerfilesBody');
    if (!tablaBody) return;

    // Esta validación ya no es necesaria aquí porque se maneja en actualizarListaPerfiles
    tablaBody.innerHTML = perfiles.map(perfil => `
        <tr>
            <td>${perfil.id}</td>
            <td>
                <div class="texto-truncado" title="${perfil.texto_original}">
                    ${perfil.texto_original.substring(0, 100)}${perfil.texto_original.length > 100 ? '...' : ''}
                </div>
            </td>
            <td>
                <span class="badge ${perfil.fuente === 'manual' ? 'badge-primary' : 'badge-secondary'}">
                    ${perfil.fuente}
                </span>
            </td>
            <td>${new Date(perfil.creado_en).toLocaleDateString()}</td>
            <td>
                <span class="badge ${perfil.publicado ? 'badge-success' : 'badge-warning'}">
                    ${perfil.publicado ? 'Publicado' : 'Borrador'}
                </span>
            </td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="verPerfil(${perfil.id})">
                    👁️ Ver
                </button>
                <button class="btn btn-sm btn-outline-success" onclick="editarPerfil(${perfil.id})">
                    ✏️ Editar
                </button>
            </td>
        </tr>
    `).join('');
}

// Función para actualizar el contador de caracteres
function actualizarContadorCaracteres() {
    const textarea = document.getElementById('descripcion');
    const contador = document.getElementById('charCount');
    
    if (textarea && contador) {
        const longitud = textarea.value.length;
        contador.textContent = `${longitud} caracteres`;
        
        // Cambiar color según la longitud
        if (longitud < 50) {
            contador.style.color = '#dc3545';
        } else if (longitud > 10000) {
            contador.style.color = '#dc3545';
        } else {
            contador.style.color = '#28a745';
        }
    }
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'success') {
    // Usar SweetAlert2 si está disponible, sino usar alert nativo
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: tipo,
            title: tipo === 'success' ? 'Éxito' : 'Error',
            text: mensaje,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    } else {
        alert(mensaje);
    }
}

// Función para validar el formulario
function validarFormulario() {
    const descripcion = document.getElementById('descripcion').value.trim();
    
    if (descripcion.length === 0) {
        mostrarNotificacion('Por favor, ingresa una descripción del puesto', 'error');
        return false;
    }
    
    if (descripcion.length < 50) {
        mostrarNotificacion('La descripción debe tener al menos 50 caracteres', 'error');
        return false;
    }
    
    if (descripcion.length > 10000) {
        mostrarNotificacion('La descripción no puede exceder los 10,000 caracteres', 'error');
        return false;
    }
    
    return true;
}

// Función para manejar el envío del formulario
async function manejarEnvioFormulario(event) {
    event.preventDefault();
    
    if (!validarFormulario()) {
        return;
    }
    
    const descripcion = document.getElementById('descripcion').value.trim();
    const botonEnviar = document.querySelector('#perfilForm button[type="submit"]');
    const textoOriginal = botonEnviar.innerHTML;
    
    try {
        // Mostrar estado de carga
        botonEnviar.innerHTML = '⏳ Guardando...';
        botonEnviar.disabled = true;
        
        // Crear perfil
        const resultado = await crearPerfilManual(descripcion);
        
        // Mostrar éxito
        mostrarNotificacion(`Perfil creado exitosamente con ID: ${resultado.job_id}`);
        
        // Limpiar formulario
        document.getElementById('perfilForm').reset();
        actualizarContadorCaracteres();
        
        // Recargar lista de perfiles si existe
        if (typeof actualizarListaPerfiles === 'function') {
            await actualizarListaPerfiles();
        }
        
        // Redirigir o mostrar opciones
        setTimeout(() => {
            if (confirm('¿Deseas ver la lista de perfiles?')) {
                window.location.href = 'lista_perfiles.php';
            }
        }, 2000);
        
    } catch (error) {
        console.error('Error:', error);
        mostrarNotificacion(error.message || 'Error al crear el perfil', 'error');
    } finally {
        // Restaurar botón
        botonEnviar.innerHTML = textoOriginal;
        botonEnviar.disabled = false;
    }
}

// Función para cargar y mostrar lista de perfiles
async function actualizarListaPerfiles() {
    const tablaBody = document.getElementById('tablaPerfilesBody');
    if (!tablaBody) return;
    
    try {
        // Mostrar estado de carga
        tablaBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando perfiles...</p>
                </td>
            </tr>
        `;
        
        const perfiles = await cargarPerfiles();
        
        if (perfiles.length === 0) {
            tablaBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <h4>No hay perfiles creados</h4>
                            <p class="mb-3">Aún no se han creado perfiles de puesto.</p>
                            <a href="crear_perfil_manual.php" class="btn btn-primary-custom">
                                ➕ Crear Primer Perfil
                            </a>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            mostrarPerfiles(perfiles);
        }
        
    } catch (error) {
        tablaBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div class="alert alert-danger">
                        Error al cargar los perfiles: ${error.message}
                    </div>
                </td>
            </tr>
        `;
    }
}

// Funciones auxiliares para acciones sobre perfiles
function verPerfil(id) {
    window.location.href = `ver_perfil.php?id=${id}`;
}

function editarPerfil(id) {
    window.location.href = `editar_perfil.php?id=${id}`;
}

// Función para inicializar la página
function inicializarPagina() {
    console.log('Inicializando página de creación de perfiles...');
    
    // Configurar event listeners
    const form = document.getElementById('perfilForm');
    const textarea = document.getElementById('descripcion');
    
    if (form) {
        form.addEventListener('submit', manejarEnvioFormulario);
    }
    
    if (textarea) {
        textarea.addEventListener('input', actualizarContadorCaracteres);
        // Inicializar contador
        actualizarContadorCaracteres();
    }
    
    // SOLO cargar lista de perfiles si estamos en una página que tiene la tabla
    const tablaPerfilesBody = document.getElementById('tablaPerfilesBody');
    if (tablaPerfilesBody) {
        actualizarListaPerfiles();
    }
    
    // Configurar botón de recarga manual
    const btnRecargar = document.getElementById('btnRecargarPerfiles');
    if (btnRecargar) {
        btnRecargar.addEventListener('click', actualizarListaPerfiles);
    }
}

// Estilos CSS adicionales para mejorar la UI
const estilosAdicionales = `
    .texto-truncado {
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .badge {
        font-size: 0.75em;
        padding: 0.35em 0.65em;
    }
    
    .badge-primary {
        background-color: #3ca6e5;
    }
    
    .badge-secondary {
        background-color: #6c757d;
    }
    
    .badge-success {
        background-color: #28a745;
    }
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .table-responsive {
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.5rem;
    }
`;

// Añadir estilos al documento
function injectarEstilos() {
    if (!document.getElementById('estilos-perfiles')) {
        const style = document.createElement('style');
        style.id = 'estilos-perfiles';
        style.textContent = estilosAdicionales;
        document.head.appendChild(style);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    injectarEstilos();
    inicializarPagina();
});

// Exportar funciones para uso global
window.crearPerfilManual = crearPerfilManual;
window.cargarPerfiles = cargarPerfiles;
window.actualizarListaPerfiles = actualizarListaPerfiles;
window.verPerfil = verPerfil;
window.editarPerfil = editarPerfil;

// Manejar errores no capturados
window.addEventListener('error', function(e) {
    console.error('Error no capturado:', e.error);
    mostrarNotificacion('Ocurrió un error inesperado', 'error');
});