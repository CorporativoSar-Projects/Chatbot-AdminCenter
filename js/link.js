document.addEventListener('DOMContentLoaded', () => {
  const btnGenerar = document.getElementById('myBtn');
  const modal = $('#myModal');
  const snippetCode = document.getElementById('snippetCode');
  //const alertContainer = document.getElementById('alertContainer');
  const copyBtn = document.getElementById('copySnippetBtn');
  const modalBody = modal.find('.modal-body')[0];
  const idAdm = btnGenerar.dataset.idadm; 

  btnGenerar.addEventListener('click', (e) => {
    e.preventDefault();

    fetch('modelo/generar_json.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id_adm=${idAdm}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        // Mostrar snippet en el modal
        snippetCode.textContent = data.snippet;

        alertContainer.innerHTML = '';

        // Abrir el modal
        modal.modal('show');
      } else {
        alert('Error al generar el JSON/snippet: ' + data.msg);
      }
    })
    .catch(err => console.error('Error en fetch:', err));
  });

  // Copiar código al portapapeles
  copyBtn.addEventListener('click', () => {
    navigator.clipboard.writeText(snippetCode.textContent)
      .then(() => {
        // Limpiar alertas anteriores
        const existingAlert = modalBody.querySelector('.alert');
        if (existingAlert) existingAlert.remove();

        // Crear alerta dentro del modal, arriba del contenido
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show';
        alertDiv.role = 'alert';
        alertDiv.textContent = 'Código copiado al portapapeles';

        // Botón de cerrar
        //const closeBtn = document.createElement('button');
        //closeBtn.type = 'button';
        //closeBtn.className = 'close';
        //closeBtn.setAttribute('aria-label', 'Cerrar');
        //closeBtn.innerHTML = '<span aria-hidden="true">&times;</span>';
        //closeBtn.addEventListener('click', () => {
          //alertDiv.remove();
        //});

        //alertDiv.appendChild(closeBtn);

        // Insertar al inicio del modal
        modalBody.prepend(alertDiv);

        // Desaparece automáticamente después de 3 segundos
        setTimeout(() => {
          alertDiv.remove();
        }, 3000);
      })
      .catch(err => console.error('Error copiando el código:', err));
  });
});
