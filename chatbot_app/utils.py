# utils.py
from django.conf import settings
from .services import EmailService
from django.utils import timezone


def check_and_notify_token_limit(token_record, request):
    """
    Verifica si se alcanzó el límite y envía notificación si es necesario
    """
    try:
        # Verificar si alcanzó el límite y aún no ha sido notificado
        if token_record.is_blocked() and not token_record.notified_limit:
            # Determinar email y nombre del usuario
            if token_record.user and token_record.user.email:
                user_email = token_record.user.email
                user_name = token_record.user.get_full_name() or token_record.user.username
            else:
                # Para usuarios anónimos, usar email configurado
                user_email = getattr(settings, 'ADMIN_EMAIL', 'admin@giintapeinnovahue.com')
                user_name = "Usuario"

            # Enviar email de notificación
            email_sent = EmailService.send_token_limit_reached_email(
                user_email, user_name, token_record
            )

            # Marcar como notificado solo si el email se envió correctamente
            if email_sent:
                token_record.notified_limit = True
                token_record.save(update_fields=['notified_limit'])
                print(f"✅ Notificación de límite registrada para {user_email}")
            else:
                print(f"⚠️ No se pudo enviar email, se reintentará en la próxima solicitud")

    except Exception as e:
        print(f"❌ Error en check_and_notify_token_limit: {str(e)}")