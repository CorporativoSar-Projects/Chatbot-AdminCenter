<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Restablecimiento de Contraseña</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f2f2f2; padding: 30px;">
  <table width="100%" cellspacing="0" cellpadding="0">
    <tr>
      <td align="center">
        <table width="600" style="background-color: #ffffff; border-radius: 8px; padding: 30px;">
          <tr>
            <td style="text-align: center;">
              <img src="{{LOGO_URL}}" alt="Logo de la empresa" style="max-width: 140px; margin-bottom: -20px;" />
            </td>
          </tr>
          <tr>
            <td>
              <h2 style="color: #2c3e50; text-align: center;">Restablecimiento de Contraseña</h2>
              <p style="font-size: 15px; color: #333;">
                Estimado/a <strong>{{NOMBRE_ADMIN}}</strong>,
              </p>
              <p style="font-size: 15px; color: #333;">
                  Hemos recibido una solicitud para restablecer la contraseña de su cuenta.
              </p>
              <p style="font-size: 15px; color: #333;">
               Su nueva contraseña es:
              </p>
              <div style="background-color: #f0f0f0; padding: 12px 20px; border-radius: 6px; text-align: center; font-size: 18px; font-weight: bold; margin: 15px 0;">
                {{NUEVA_CONTRASENA}}
              </div>
              <p style="font-size: 15px; color: #333;">
                  Por favor, utilice esta contraseña para iniciar sesión.
              </p>
              <p style="font-size: 15px; color: #333;">
                  Puede acceder a la plataforma haciendo clic en el siguiente botón:
              </p>
              <div style="text-align: center; margin: 30px 0;">
                <a href="http://localhost/Chatbot-AdminCenter/index.php" style="background-color: #ffb703; color: #0f0d0d; text-decoration: none; padding: 12px 24px; border-radius: 50px;">
                  Acceder
                </a>
              </div>
              <p style="font-size: 13px; color: #777;">
                Si no solicitó este cambio, por favor comuníquese inmediatamente con nuestro equipo de soporte.
              </p>
              <p style="font-size: 13px; color: #777;">
                Atentamente,<br>
                <strong>Equipo de Soporte - GIINTAPE INNOVAHUE</strong>
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
