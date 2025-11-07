<!-- modalIntegracion.php -->
<div class="modal fade" id="sftpModal" tabindex="-1" role="dialog" aria-labelledby="sftpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="sftpModalLabel">Configuración de Integración</h5>
        <span class="cerrar-modal" id="cerrarIntegracion" data-dismiss="modal">&times;</span>
      </div>

      <div class="modal-body">
        <form id="formIntegracionSFTP">
          <div class="d-flex align-items-center mb-3">
            <input class="form-check-input small-checkbox me-2" type="checkbox" id="sftpCheckbox">
            <label for="sftpCheckbox" class="m-0">Activar integración SFTP</label>
          </div>

          <label>Servidor:</label>
          <input type="text" class="form-control custom-input" name="servidor" required />

          <label>Puerto:</label>
          <input type="text" class="form-control custom-input" name="puerto" value="22" readonly />

          <label>Usuario:</label>
          <input type="text" class="form-control custom-input" name="usuario" required />

          <label>Contraseña:</label>
          <input type="password" class="form-control custom-input" name="contrasena" />

          <label>Ruta de Destino:</label>
          <input type="text" class="form-control custom-input" name="rutaDestino" required />
        </form>
      </div>

      <div class="modal-footer1">
        <button class="submit-button-form" type="submit" form="formIntegracionSFTP">Guardar</button>
      </div>

    </div>
  </div>
</div>
