<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Restablecimiento de Contraseña</title>
  <style>
    /* Media query para móviles */
    @media only screen and (max-width: 620px) {
      .container {
        width: 100% !important;
        padding: 15px !important;
      }
      .btn {
        width: 100% !important;
        padding: 12px !important;
      }
      .content p, .content h2 {
        font-size: 16px !important;
      }
      .logo {
        max-width: 120px !important;
      }
      .footer-logo {
        max-width: 50px !important;
      }
    }
  </style>
</head>
<body style="margin:0; padding:30px; background-color:#f2f2f2; font-family: Arial, sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center">
        <table class="container" width="600" cellpadding="0" cellspacing="0" style="background-color:#fff; border-radius:8px; padding:30px; max-width:100%;">
          <tr>
            <td align="center">
              <img src="{{LOGO_URL}}" alt="Logo de la empresa" class="logo" style="max-width:140px; margin-bottom:0;">
            </td>
          </tr>
          <tr>
            <td class="content" style="padding-top:15px;">
              <h2 style="color:#2c3e50; text-align:center; margin-bottom:10px;">Restablecimiento de Contraseña</h2>
              <p style="font-size:15px; color:#333; margin:5px 0;">Estimado/a <strong>{{NOMBRE_ADMIN}}</strong>,</p>
              <p style="font-size:15px; color:#333; margin:5px 0;">
                Hemos recibido una solicitud para restablecer la contraseña de su cuenta.
              </p>
              <p style="font-size:15px; color:#333; margin:5px 0;">Su nueva contraseña es:</p>
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center" style="background-color:#f0f0f0; padding:12px 20px; border-radius:6px; font-size:18px; font-weight:bold; margin:15px 0;">
                    {{NUEVA_CONTRASENA}}
                  </td>
                </tr>
              </table>
              <p style="font-size:15px; color:#333; margin:5px 0;">
                Por favor, utilice esta contraseña para iniciar sesión.
              </p>
              <p style="font-size:15px; color:#333; margin:5px 0;">
                Puede acceder a la plataforma haciendo clic en el siguiente botón:
              </p>
              <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                  <td align="center" style="padding:20px 0;">
                    <a href="http://localhost/Chatbot-AdminCenter/index.php" class="btn" style="background-color:#ffb703; color:#0f0d0d; text-decoration:none; padding:12px 24px; border-radius:50px; display:inline-block;">
                      Acceder
                    </a>
                  </td>
                </tr>
              </table>
              <p style="font-size:13px; color:#777; margin:5px 0;">
                Si no solicitó este cambio, por favor comuníquese inmediatamente con nuestro equipo de soporte.
              </p>
              <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;">
                <tr>
                  <td style="font-size:13px; color:#777; text-align:left;">
                    <p style="margin:0;">
                      Atentamente,<br>
                      <strong>Equipo de Soporte de GIINTAPE INNOVAHUE</strong>
                    </p>
                  </td>
                  <td style="text-align:right; vertical-align:middle;">
                    <img src="{{LOGO_PIE_URL}}" alt="Logo pequeño" class="footer-logo" style="max-width:60px; vertical-align:middle; margin-left:10px;">
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
