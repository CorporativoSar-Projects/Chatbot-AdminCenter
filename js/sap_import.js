
const API_BASE = window.API_BASE || 'http://localhost:8000/';

$(document).ready(function() {

  // --- Importar puesto individual desde JSON ---
  $('#btnImportarJson').click(function() {
    let jsonData = $('#formImportarJson textarea').val();

    if (!jsonData.trim()) {
      alert('Ingresa un JSON válido.');
      return;
    }

    try {
      jsonData = JSON.parse(jsonData);
    } catch(e) {
      alert('JSON inválido. Corrígelo.');
      return;
    }

    $.ajax({
      url: '/sap/importar-puesto/',
      type: 'POST',
      contentType: 'application/json',
      data: JSON.stringify(jsonData),
      success: function(res) {
        alert(res.message);
        $('#btnListarPuestos').click(); // actualizar tabla
      },
      error: function(err) {
        console.error(err);
        alert('Error al importar puesto: ' + JSON.stringify(err.responseJSON));
      }
    });
  });

  // --- Listar puestos almacenados ---
  $('#btnListarPuestos').click(function() {
    $.ajax({
      url: '/sap/puestos/',
      type: 'GET',
      success: function(res) {
        const tbody = $('#tablaPuestos tbody');
        tbody.empty();

        if(res.length === 0){
          tbody.append('<tr><td colspan="6">No hay puestos almacenados</td></tr>');
          return;
        }

        res.forEach(puesto => {
          tbody.append(`
            <tr>
              <td>${puesto.id_requisicion}</td>
              <td>${puesto.categoria}</td>
              <td>${puesto.titulo}</td>
              <td><a href="${puesto.link}" target="_blank">Ver</a></td>
              <td>${puesto.ubicacion}</td>
              <td>${puesto.fecha_importacion}</td>
            </tr>
          `);
        });
      },
      error: function(err) {
        console.error(err);
        alert('Error al obtener los puestos.');
      }
    });
  });

  // --- Importar puestos desde CSV ---
  $('#btnImportarCsv').click(function() {
    const form = $('#formImportarCsv')[0];
    const formData = new FormData(form);

    if (!formData.get('file')) {
      alert('Selecciona un archivo CSV.');
      return;
    }

    $.ajax({
      url: '/sap/importar-puesto-csv/',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(res) {
        alert(`${res.message}. Total registros: ${res.total}`);
        $('#btnListarPuestos').click(); // actualizar tabla
      },
      error: function(err) {
        console.error(err);
        alert('Error al importar CSV: ' + JSON.stringify(err.responseJSON));
      }
    });
  });

  // --- Importar puestos desde URLs configuradas ---
  $('#btnImportarUrls').click(function() {
    $.ajax({
      url: '/sap/importar-puestos-urls/',
      type: 'POST',
      success: function(res) {
        alert(`${res.message}. Total registros importados: ${res.total_registros_importados}`);
        if(res.errores && res.errores.length > 0){
          console.warn('Errores en la importación:', res.errores);
        }
        $('#btnListarPuestos').click(); // actualizar tabla
      },
      error: function(err) {
        console.error(err);
        alert('Error al importar desde URLs: ' + JSON.stringify(err.responseJSON));
      }
    });
  });

  // --- Inicializar: listar puestos al cargar la página ---
  $('#btnListarPuestos').click();

});

