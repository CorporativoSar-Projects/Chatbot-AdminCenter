from django.db import models
from django.utils import timezone
from django.contrib.auth.models import User  # opcional si tienes usuarios


class ChatSession(models.Model):
    started_at = models.DateTimeField(auto_now_add=True)
    ended_at = models.DateTimeField(null=True, blank=True)
    user = models.ForeignKey(User, null=True, blank=True, on_delete=models.SET_NULL)

    def __str__(self):
        return f"Session {self.id} - {self.user or 'Anon'}"


class ChatMessage(models.Model):
    session = models.ForeignKey(ChatSession, related_name='messages', on_delete=models.CASCADE, null=True)
    user_message = models.TextField()
    bot_response = models.TextField()
    created_at = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"User: {self.user_message}, Bot: {self.bot_response}"


class ChatErrorLog(models.Model):
    user = models.ForeignKey(User, null=True, blank=True, on_delete=models.SET_NULL)
    session_id = models.IntegerField(null=True, blank=True)
    user_message = models.TextField()
    error_code = models.IntegerField(null=True, blank=True)
    error_text = models.TextField()
    created_at = models.DateTimeField(default=timezone.now)

    def __str__(self):
        return f"Error {self.id} - User {self.user or 'Anon'}"


class TokenUsage(models.Model):
    user = models.ForeignKey(User, on_delete=models.CASCADE, null=True, blank=True)
    session = models.ForeignKey(ChatSession, null=True, blank=True, on_delete=models.SET_NULL)
    # PARA USUARIOS ANÓNIMOS
    anon_id = models.CharField(max_length=100, null=True, blank=True)
    month = models.DateField(default=timezone.now)
    input_tokens = models.PositiveIntegerField(default=0)
    output_tokens = models.PositiveIntegerField(default=0)
    memory_tokens = models.PositiveIntegerField(default=0)
    notified_limit = models.BooleanField(default=False)

    MAX_TOKENS = 1000000

    def remaining_input(self):
        return max(self.MAX_TOKENS - self.input_tokens, 0)

    def remaining_output(self):
        return max(self.MAX_TOKENS - self.output_tokens, 0)

    def remaining_memory(self):
        return max(self.MAX_TOKENS - self.memory_tokens, 0)

    def is_blocked(self):
        """Indica si el usuario ha alcanzado el límite de tokens de entrada o salida"""
        return self.input_tokens >= self.MAX_TOKENS or self.output_tokens >= self.MAX_TOKENS

    def can_use_api(self):
        """
        Determina si el usuario puede seguir usando la API.
        Si se alcanzó el límite, devuelve False.
        También resetea automáticamente los tokens si cambió el ciclo mensual.
        """
        now = timezone.now().date()
        month_start = now.replace(day=1)

        # Si el registro pertenece a un mes anterior → reiniciar automáticamente
        if self.month != month_start:
            self.month = month_start
            self.input_tokens = 0
            self.output_tokens = 0
            self.memory_tokens = 0
            self.notified_limit = False  # Reinicia notificación mensual
            self.save()
            return True  # ✅ Nuevo ciclo: puede usar la API

        # Si ya alcanzó o superó el límite → bloquear
        if self.input_tokens >= self.MAX_TOKENS or self.output_tokens >= self.MAX_TOKENS:
            return False

        # Si aún no llegó al límite, permitir uso
        return True

    def __str__(self):
        user_str = self.user.username if self.user else "Anon"
        return f"Tokens {user_str} - {self.month}"


