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


''' Conectando con los modelos en mysql '''


class Empresa(models.Model):
    id_emp = models.CharField(max_length=25, primary_key=True)
    RFC_emp = models.CharField(max_length=13)
    nombre_emp = models.CharField(max_length=50)
    logotipo_emp = models.CharField(max_length=255)
    sitioweb_emp = models.CharField(max_length=255)
    codigoPostal_emp = models.CharField(max_length=10)
    estado_emp = models.CharField(max_length=100)
    url_cs_emp = models.CharField(max_length=255)

    class Meta:
        db_table = 'empresa'
        managed = False   # << EVITA MIGRACIONES


class Administrador(models.Model):
    id_adm = models.AutoField(primary_key=True)
    correo_adm = models.CharField(max_length=100)
    nombre_adm = models.CharField(max_length=50)
    apellidop_adm = models.CharField(max_length=50)
    apellidom_adm = models.CharField(max_length=50)
    tel_adm = models.CharField(max_length=20)
    pass_adm = models.CharField(max_length=300)

    Empresa_id_emp = models.ForeignKey(
        Empresa,
        on_delete=models.CASCADE,
        db_column='Empresa_id_emp'
    )

    class Meta:
        db_table = 'administrador'
        managed = False


''' Modelos normales '''


class AIModelConfig(models.Model):
    FUNCTION_CHOICES = [
        ('mejorar_descripcion', 'Mejorar descripción'),
        ('analisis_cv', 'Análisis CV'),
        ('chat_general', 'Chat general'),
    ]

    function = models.CharField(max_length=64, choices=FUNCTION_CHOICES, unique=True)
    model_name = models.CharField(max_length=128)
    description = models.TextField(blank=True, null=True)
    updated_at = models.DateTimeField(auto_now=True)


class PromptTemplate(models.Model):
    name = models.CharField(max_length=128, unique=True)
    function = models.CharField(max_length=64, blank=True)
    template = models.TextField()
    active = models.BooleanField(default=True)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)


class JobDescription(models.Model):
    SOURCE_MANUAL = 'manual'
    SOURCE_SAP = 'sap'

    texto_original = models.TextField()
    fuente = models.CharField(max_length=16, choices=[
        (SOURCE_MANUAL, 'Manual'),
        (SOURCE_SAP, 'SAP SSFF')
    ], default=SOURCE_MANUAL)

    # USANDO ADMINISTRADOR COMO USUARIO
    usuario = models.ForeignKey(
        Administrador,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        db_column='id_adm'
    )

    external_id = models.CharField(max_length=256, blank=True, null=True)
    creado_en = models.DateTimeField(auto_now_add=True)
    actualizado_en = models.DateTimeField(auto_now=True)
    publicado = models.BooleanField(default=False)

    latest_revision = models.ForeignKey(
        'JobDescriptionRevision',
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name='+'
    )

    def __str__(self):
        return f"{self.texto_original[:50]}... ({self.usuario.nombre_adm if self.usuario else 'Anon'})"

    # ------------------- Helper para crear revisiones -------------------
    def crear_revision(self, texto_mejorado, creado_por, prompt_usado=None,
                       modelo_usado=None, prompt_template=None,
                       input_tokens=0, output_tokens=0, memory_tokens=0,
                       is_reviewed=False, is_published=False):
        """
        Crea una nueva revisión para este JobDescription y actualiza latest_revision.
        """
        # Determinar la versión siguiente
        ultima = self.latest_revision.version if self.latest_revision else 0
        nueva_version = ultima + 1

        revision = JobDescriptionRevision.objects.create(
            job_description=self,
            texto_mejorado=texto_mejorado,
            creado_por=creado_por,
            prompt_usado=prompt_usado or "",
            modelo_usado=modelo_usado or "",
            prompt_template=prompt_template,
            version=nueva_version,
            input_tokens=input_tokens,
            output_tokens=output_tokens,
            memory_tokens=memory_tokens,
            is_reviewed=is_reviewed,
            is_published=is_published
        )

        # Actualizar latest_revision
        self.latest_revision = revision
        self.save(update_fields=['latest_revision'])

        return revision


class JobDescriptionRevision(models.Model):
    job_description = models.ForeignKey(JobDescription, on_delete=models.CASCADE, related_name='revisions')

    texto_mejorado = models.TextField()
    salida_estructurada = models.JSONField(blank=True, null=True)

    prompt_usado = models.TextField()
    modelo_usado = models.CharField(max_length=128)
    prompt_template = models.ForeignKey(PromptTemplate, on_delete=models.SET_NULL, null=True, blank=True)

    # TAMBIÉN CREADO POR ADMINISTRADOR
    creado_por = models.ForeignKey(
        Administrador,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        db_column='id_adm'
    )

    version = models.PositiveIntegerField(default=1)
    creado_en = models.DateTimeField(auto_now_add=True)
    input_tokens = models.PositiveIntegerField(default=0)
    output_tokens = models.PositiveIntegerField(default=0)
    memory_tokens = models.PositiveIntegerField(default=0)
    is_reviewed = models.BooleanField(default=False)
    is_published = models.BooleanField(default=False)


class AIAuditLog(models.Model):
    revision = models.OneToOneField(JobDescriptionRevision, on_delete=models.CASCADE, null=True, blank=True)
    request_payload = models.JSONField(blank=True, null=True)
    response_payload = models.JSONField(blank=True, null=True)
    error_text = models.TextField(blank=True, null=True)
    created_at = models.DateTimeField(auto_now_add=True)


class PuestoSAP(models.Model):
    id_requisicion = models.IntegerField(unique=True)
    categoria = models.CharField(max_length=255)
    titulo = models.CharField(max_length=255)
    link = models.URLField()
    ubicacion = models.CharField(max_length=255)
    fecha_importacion = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.titulo} - {self.categoria}"


class InformeSAP(models.Model):
    nombre = models.CharField(max_length=255)
    descripcion = models.TextField(blank=True, null=True)
    url = models.URLField(unique=True)
    fecha_configuracion = models.DateTimeField(auto_now_add=True)
    ultima_descarga = models.DateTimeField(null=True, blank=True)

    def __str__(self):
        return f"{self.nombre} ({self.url})"


class Candidato(models.Model):
    id_candidate = models.AutoField(primary_key=True)
    correo_candidate = models.CharField(max_length=50)
    nombre_candidate = models.CharField(max_length=45)
    apellidop_candidate = models.CharField(max_length=50)
    apellidom_candidate = models.CharField(max_length=50)
    tel_candidate = models.CharField(max_length=20)
    CV_candidate = models.CharField(max_length=255)
    CV_id_onedrive = models.CharField(max_length=100)
    token_verificacion = models.CharField(max_length=10, null=True, blank=True)
    token_expira = models.DateTimeField(null=True, blank=True)
    token_validado = models.BooleanField(default=False)

    class Meta:
        db_table = "candidato"
        managed = False  # No tocar la tabla, solo leer/escribir


class ComparacionCVPuesto(models.Model):
    candidato = models.ForeignKey(Candidato, on_delete=models.CASCADE, related_name="comparaciones")
    id_puesto = models.CharField(max_length=255)
    resultado = models.TextField()
    score = models.IntegerField()
    fecha = models.DateTimeField(auto_now_add=True)

    def __str__(self):
        return f"{self.candidato.nombre_candidate} vs {self.id_puesto} -> {self.score}%"


class Puesto(models.Model):
    req_id = models.CharField(max_length=50, unique=True)
    descripcion_original = models.TextField()
    descripcion_mejorada = models.TextField(null=True, blank=True)
    fecha_mejora = models.DateTimeField(null=True, blank=True)


