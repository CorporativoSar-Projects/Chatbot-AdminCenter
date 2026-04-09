<!-- modalIntegracion.php -->
<div class="modal fade" id="sftpModal" tabindex="-1" role="dialog" aria-labelledby="sftpModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="sftpModalLabel">Configuración de Integración</h5>
        <span class="cerrar-modal" id="cerrarIntegracion" data-dismiss="modal">&times;</span>
      </div>

      <div class="modal-body" style="margin-left: 40px">
        <!-- Selector de tipo de integración -->
        <div class="mb-4">
          <label for="tipoIntegracion" class="form-label fw-bold">Tipo de Integración:</label>
          <select id="tipoIntegracion" class="form-select custom-input">
            <option value="estandar" <?php echo ($sftpActivo == 1) ? '' : 'selected'; ?>>Integración Estándar</option>
            <option value="sftp" <?php echo ($sftpActivo == 1) ? 'selected' : ''; ?>>Integración SFTP</option>
          </select>
        </div>

        <!-- Formulario Integración Estándar -->
        <form id="formIntegracionEstandar">
          <div class="d-flex align-items-center mb-3">
            <input class="form-check-input small-checkbox me-2" type="checkbox" id="estandarCheckbox"
              <?php echo ($sftpActivo == 1) ? '' : 'checked'; ?>>
            <label for="sftpCheckbox" class="m-0">Activar integración Estándar</label>
          </div>

          <label>URL del sitio de carreras:</label>
          <input type="text" class="form-control custom-input" name="url_estandar" placeholder="https://tusitio.com/carreras" required />

          <input type="hidden" name="tipo_integracion" id="tipoIntegracionHidden" value="estandar">
        </form>

        <!-- 🔹 Formulario Integración SFTP -->
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
