document.addEventListener('DOMContentLoaded', () => {
  const btnGenerar = document.getElementById('myBtn');
  const modal = $('#myModal');
  const snippetCode = document.getElementById('snippetCode');
  const alertContainer = document.getElementById('alertContainer');
  const copyBtn = document.getElementById('copySnippetBtn');
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
        alertContainer.innerHTML = `
          <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
             Código copiado al portapapeles
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>`;
      })
      .catch(err => console.error('Error copiando el código:', err));
  });
});
