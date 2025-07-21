<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro Exitoso</title>
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
              <h2 style="color: #2c3e50; text-align: center;">Bienvenido(a) – ID de tu empresa asignado</h2>
              <p style="font-size: 15px; color: #333;">
                Estimado/a <strong>{{NOMBRE_ADMIN}}</strong>,
              </p>
              <p style="font-size: 15px; color: #333;">
                Nos complace informarle que el registro de su empresa, <strong>{{NOMBRE_EMPRESA}}</strong>, ha sido completado con éxito en nuestra plataforma.
              </p>
              <p style="font-size: 15px; color: #333;">
                El <strong>ID asignado a su empresa</strong> es el siguiente:
              </p>
              <div style="background-color: #f0f0f0; padding: 12px 20px; border-radius: 6px; text-align: center; font-size: 18px; font-weight: bold; margin: 15px 0;">
                {{ID_EMPRESA}}
              </div>
              <p style="font-size: 15px; color: #333;">
                Le recomendamos guardar este identificador, ya que será necesario para acceder al sistema.
              </p>
              <p style="font-size: 15px; color: #333;">
               Para iniciar sesión, puede hacer clic en el siguiente enlace:
              </p>
              <div style="text-align: center; margin: 30px 0;">
                <a href="{{URL_LOGIN}}" style="background-color: #ffb703; color: #0f0d0d; text-decoration: none; padding: 12px 24px; border-radius: 50px;">
                  Acceder a la plataforma
                </a>
              </div>
              <p style="font-size: 13px; color: #777;">
                Si tiene alguna pregunta o necesita asistencia, por favor no dude en contactar a nuestro equipo de Soporte .
              </p>
              <p style="font-size: 13px; color: #777;">
                Atentamente,<br>
                <strong>Equipo de Soporte de GIINTAPE INNOVAHUE</strong>
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
