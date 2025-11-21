// panel-admin-ia.js
// JS para el panel IA (módulo ES6)

const API_BASE = window.API_IA_BASE || 'http://localhost:8000/api'; // cambia según tu entorno

async function apiFetch(path, opts = {}){
  const res = await fetch(API_BASE + path, {
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include', // si usas cookies de sesión
    ...opts
  });
  return res.json();
}

// ---- MODELO ----
const selectFunction = document.getElementById('selectFunction');
const inputModelName = document.getElementById('inputModelName');
const btnSaveModel = document.getElementById('btnSaveModel');
const modelSaveMsg = document.getElementById('modelSaveMsg');

btnSaveModel.addEventListener('click', async (e) =>{
  e.preventDefault();
  const funcion = selectFunction.value;
  const model_name = inputModelName.value.trim();
  if(!model_name) return alert('Ingresa un nombre de modelo');
  const data = await apiFetch('model/set/', {
    method: 'POST',
    body: JSON.stringify({ function: funcion, model_name })
  });
  if(data.status === 'ok'){
    modelSaveMsg.textContent = 'Guardado';
    setTimeout(()=> modelSaveMsg.textContent = '', 2000);
  } else {
    alert(data.error || 'Error al guardar');
  }
});

// ---- CREAR PROMPT ----
const formCreatePrompt = document.getElementById('formCreatePrompt');
const promptList = document.getElementById('promptList');

formCreatePrompt.addEventListener('submit', async (e)=>{
  e.preventDefault();
  const name = document.getElementById('promptName').value.trim();
  const func = document.getElementById('promptFunction').value;
  const template = document.getElementById('promptTemplate').value.trim();
  if(!name || !template) return alert('Completa todos los campos');

  const res = await apiFetch('prompt/create/', {
    method: 'POST',
    body: JSON.stringify({ name, function: func, template })
  });
  if(res.status === 'ok'){
    alert('Template creado');
    loadPrompts();
    formCreatePrompt.reset();
  } else {
    alert(res.error || 'Error');
  }
});

async function loadPrompts(){
  // Asumimos que crearás un endpoint GET /prompt/list/ en Django (puedo añadirlo luego)
  try{
    const res = await apiFetch('prompt/list/');
    promptList.innerHTML = '';
    if(res.data && res.data.length){
      res.data.forEach(p =>{
        const div = document.createElement('div');
        div.className = 'd-flex justify-content-between align-items-center mb-2';
        div.innerHTML = `
          <div>
            <strong>${p.name}</strong> <small class="text-muted">(${p.function})</small>
            <div class="small">${p.template.substring(0,120)}${p.template.length>120?'...':''}</div>
          </div>
          <div>
            <button class="btn btn-sm btn-outline-primary" data-id="${p.id}" onclick="activatePrompt(${p.id})">Activar</button>
          </div>
        `;
        promptList.appendChild(div);
      });
    } else {
      promptList.innerHTML = '<p class="small-muted">No hay plantillas</p>';
    }
  }catch(err){ console.error(err); promptList.innerHTML = '<p class="text-danger">Error al cargar</p>' }
}

window.activatePrompt = async function(id){
  const res = await apiFetch('prompt/activate/', { method: 'POST', body: JSON.stringify({ template_id: id }) });
  if(res.status === 'ok'){ alert('Plantilla activada'); loadPrompts(); }
  else alert(res.error || 'Error');
}

// ---- REVISIONS ----
const revisionsList = document.getElementById('revisionsList');
const btnRefreshRevisions = document.getElementById('btnRefreshRevisions');

btnRefreshRevisions.addEventListener('click', loadRevisions);

async function loadRevisions(){
  const res = await apiFetch('revisions/list/'); // crea endpoint en Django
  revisionsList.innerHTML = '';
  if(res.data && res.data.length){
    res.data.forEach(r=>{
      const el = document.createElement('div');
      el.className = 'mb-2 border p-2 rounded';
      el.innerHTML = `
        <div><strong>JD #${r.job_description_id}</strong> - v${r.version} - ${r.creado_en}</div>
        <div class="small">Modelo: ${r.modelo_usado}</div>
        <div class="mt-2">${r.texto_mejorado.substring(0,300)}${r.texto_mejorado.length>300?'...':''}</div>
        <div class="mt-2">
          <button class="btn btn-sm btn-success" onclick="publicarRevision(${r.id})">Publicar</button>
        </div>
      `;
      revisionsList.appendChild(el);
    })
  } else revisionsList.innerHTML = '<p class="small-muted">Sin revisiones</p>';
}

window.publicarRevision = async function(id){
  const res = await apiFetch('revision/publicar/', { method: 'POST', body: JSON.stringify({ revision_id: id })});
  if(res.status==='ok'){ alert('Publicada'); loadRevisions(); } else alert(res.error || 'Error');
}

// ---- CONFIGURACIÓN ----
const configForm = document.getElementById('configForm');
const configMessage = document.getElementById('configMessage');

configForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  const formData = new FormData(configForm);
  const configData = {
    max_tokens: parseInt(formData.get('max_tokens')),
    temperature: parseFloat(formData.get('temperature')),
    top_p: parseFloat(formData.get('top_p')),
    frequency_penalty: parseFloat(formData.get('frequency_penalty')),
    presence_penalty: parseFloat(formData.get('presence_penalty'))
  };

  try {
    const res = await apiFetch('config/update/', {
      method: 'POST',
      body: JSON.stringify(configData)
    });
    
    if(res.status === 'ok') {
      configMessage.textContent = 'Configuración guardada correctamente';
      configMessage.className = 'alert alert-success';
      setTimeout(() => {
        configMessage.textContent = '';
        configMessage.className = '';
      }, 3000);
    } else {
      throw new Error(res.error || 'Error al guardar configuración');
    }
  } catch (error) {
    configMessage.textContent = error.message;
    configMessage.className = 'alert alert-danger';
  }
});

// ---- INICIALIZACIÓN ----
document.addEventListener('DOMContentLoaded', function() {
  // Cargar datos iniciales
  loadPrompts();
  loadRevisions();
  
  // Cargar configuración actual
  loadCurrentConfig();
});

async function loadCurrentConfig() {
  try {
    const res = await apiFetch('config/get/');
    if(res.status === 'ok' && res.data) {
      const config = res.data;
      document.getElementById('max_tokens').value = config.max_tokens || 1000;
      document.getElementById('temperature').value = config.temperature || 0.7;
      document.getElementById('top_p').value = config.top_p || 1.0;
      document.getElementById('frequency_penalty').value = config.frequency_penalty || 0;
      document.getElementById('presence_penalty').value = config.presence_penalty || 0;
    }
  } catch (error) {
    console.error('Error cargando configuración:', error);
  }
}

// Utilidades
function showNotification(message, type = 'success') {
  // Implementar notificación toast si es necesario
  console.log(`${type}: ${message}`);
}

