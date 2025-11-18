<?php
include 'modelo/consultas_menu.php'; // Validación del usuario
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Historial de Requisiciones</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2>Historial de Requisiciones</h2>
  <table id="tablaRequisiciones" class="table table-striped table-bordered">
    <thead class="thead-dark">
      <tr>
        <th>ID</th>
        <th>Título del puesto</th>
        <th>Candidatos</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Datos simulados
      $requisiciones = [
        ['id'=>101, 'titulo'=>'Desarrollador PHP', 'num_candidatos'=>3],
        ['id'=>102, 'titulo'=>'QA Analyst', 'num_candidatos'=>2],
        ['id'=>103, 'titulo'=>'Frontend Developer', 'num_candidatos'=>4],
      ];
      foreach($requisiciones as $r):
      ?>
      <tr>
        <td><?php echo $r['id']; ?></td>
        <td><?php echo $r['titulo']; ?></td>
        <td>
          <a href="candidatos.php?id_requisicion=<?php echo $r['id']; ?>">
            <?php echo $r['num_candidatos']; ?>
          </a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function(){
  $('#tablaRequisiciones').DataTable({
    paging: true,
    pageLength: 5,
    dom: 'Bfrtip',
    buttons: [
      { extend: 'excelHtml5', title: 'Historial_Requisiciones' },
      { extend: 'csvHtml5', title: 'Historial_Requisiciones' }
    ]
  });
});
</script>
</body>
</html>


