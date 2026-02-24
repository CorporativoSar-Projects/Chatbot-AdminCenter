<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Límite de Tokens Alcanzado</title>
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
              <h2 style="color: #2c3e50; text-align: center;">Límite de Tokens Alcanzado</h2>
              <p style="font-size: 15px; color: #333;">
                Estimado/a <strong>{{NOMBRE_ADMIN}}</strong>,
              </p>
              <p style="font-size: 15px; color: #333;">
                Le informamos que <strong>ha alcanzado el límite mensual de tokens</strong> en nuestra plataforma de IA.
              </p>
              
              <div style="background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 6px; padding: 15px; margin: 15px 0;">
                <p style="font-size: 14px; color: #856404; margin: 0;">
                  ⚠️ <strong>Estado actual:</strong> El servicio de IA ha sido temporalmente deshabilitado hasta el próximo ciclo mensual.
                </p>
              </div>
              
              <p style="font-size: 15px; color: #333;">
                <strong>Detalles del uso:</strong>
              </p>
              <ul style="font-size: 14px; color: #333;">
                <li>Tokens de entrada utilizados: {{TOKEN_USAGE.input_tokens}}/{{MAX_TOKENS}}</li>
                <li>Tokens de salida utilizados: {{TOKEN_USAGE.output_tokens}}/{{MAX_TOKENS}}</li>
                <li>Fecha de límite alcanzado: {{LIMIT_DATE|date:"d/m/Y H:i"}}</li>
                <li>Próximo reinicio: {{NEXT_CYCLE|date:"d/m/Y"}}</li>
              </ul>
              
              <p style="font-size: 15px; color: #333;">
                El servicio se reactivará automáticamente el <strong>{{NEXT_CYCLE|date:"d/m/Y"}}</strong> cuando comience el nuevo ciclo mensual.
              </p>
              
              <p style="font-size: 15px; color: #333;">
                Mientras tanto, puede seguir accediendo a la plataforma para consultar información histórica y gestionar otros aspectos de su cuenta.
              </p>
              
              <div style="text-align: center; margin: 30px 0;">
                <a href="{{URL_LOGIN}}" style="background-color: #ffb703; color: #0f0d0d; text-decoration: none; padding: 12px 24px; border-radius: 50px;">
                  Acceder a la plataforma
                </a>
              </div>
              
              <p style="font-size: 13px; color: #777;">
                Si necesita aumentar su límite de tokens o tiene alguna pregunta, por favor contacte a nuestro equipo de Soporte.
              </p>
               
              <!-- PIE CON LOGO A LA DERECHA -->
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
