<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Código de verificación</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f2f2f2; padding: 30px;">
  <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td align="center">
        <table width="600" style="background-color: #ffffff; border-radius: 8px; padding: 30px;">
          <!-- LOGO SUPERIOR -->
          <tr>
            <td style="text-align: center;">
              <img src="{{LOGO_URL}}" alt="Logo de la empresa" style="max-width: 140px; margin-bottom: -20px;" />
            </td>
          </tr>

          <!-- CONTENIDO -->
          <tr>
            <td>
              <h2 style="color: #2c3e50; text-align: center;">Código de verificación</h2>
              <p style="font-size: 15px; color: #333;">
                Estimado/a candidato/a,
              </p>
              <p style="font-size: 15px; color: #333;">
                Para poder ingresar tu información de postulación, necesitamos que verifiques tu identidad.
              </p>
              <p style="font-size: 15px; color: #333;">
                Tu <strong>código de verificación</strong> es:
              </p>
              <div style="background-color: #f0f0f0; padding: 12px 20px; border-radius: 6px; text-align: center; font-size: 22px; font-weight: bold; margin: 15px 0;">
                {{TOKEN}}
              </div>
              <p style="font-size: 15px; color: #333;">
                Con este código podras tener acceso por 30 min.
              </p>
              <p style="font-size: 15px; color: #333;">
                Ingresa este código en la plataforma para continuar con la actualización de tu información.
              </p>
              <p style="font-size: 13px; color: #777;">
                Si no solicitaste este código, puedes ignorar este correo.
              </p>
            <!-- FIRMA CON LOGO AL LADO -->
              <table width="100%" cellspacing="0" cellpadding="0" style="margin-top: 20px;">
                <tr>
                  <td style="font-size: 13px; color: #777; text-align: left;">
                    <p style="margin: 0;">
                      Atentamente,<br>
                      <strong>Equipo de Soporte de GIINTAPE INNOVAHUE</strong>
                    </p>
                  </td>
                  <td style="text-align: right; vertical-align: middle;">
                    <img src="{{LOGO_PIE_URL}}" alt="Logo pequeño" style="max-width: 60px; vertical-align: middle; margin-left: 10px;" />
                  </td>
                </tr>
              </table>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
