# services.py
from django.core.mail import EmailMultiAlternatives
from django.template.loader import render_to_string
from django.utils.html import strip_tags
from django.conf import settings
from django.utils import timezone


class EmailService:
    @staticmethod
    def send_token_limit_reached_email(user_email, user_name, token_record):
        """
        Envía correo notificando que se alcanzó el límite de tokens
        """
        try:
            # Calcular próximo ciclo
            next_month = token_record.month.month % 12 + 1
            next_year = token_record.month.year + (1 if next_month == 1 else 0)
            next_cycle = timezone.datetime(next_year, next_month, 1).date()

            # Contexto para el template
            context = {
                'NOMBRE_ADMIN': user_name,
                'LOGO_URL': getattr(settings, 'LOGO_URL', 'https://tu-dominio.com/static/images/logo.png'),
                'LOGO_PIE_URL': getattr(settings, 'LOGO_PIE_URL', 'https://tu-dominio.com/static/images/logo-pie.png'),
                'URL_LOGIN': getattr(settings, 'LOGIN_URL', 'https://tu-dominio.com/login'),
                'TOKEN_USAGE': token_record,
                'LIMIT_DATE': timezone.now(),
                'NEXT_CYCLE': next_cycle,
                'MAX_TOKENS': token_record.MAX_TOKENS
            }

            # Renderizar template HTML
            html_content = render_to_string('emails/token_limit_reached.html', context)
            text_content = strip_tags(html_content)

            # Crear y enviar email
            email = EmailMultiAlternatives(
                subject='Límite de Tokens Alcanzado - GIINTAPE INNOVAHUE',
                body=text_content,
                from_email=getattr(settings, 'DEFAULT_FROM_EMAIL', 'notificaciones@giintapeinnovahue.com'),
                to=[user_email],
                reply_to=[getattr(settings, 'SUPPORT_EMAIL', 'soporte@giintapeinnovahue.com')]
            )
            email.attach_alternative(html_content, "text/html")
            email.send()

            print(f"✅ Email de límite de tokens enviado a {user_email}")
            return True

        except Exception as e:
            print(f"❌ Error enviando email de límite de tokens: {str(e)}")
            return False
