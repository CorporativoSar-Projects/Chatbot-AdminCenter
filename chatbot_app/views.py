import json
import os
import traceback

from django.contrib.auth.models import User
from django.http import JsonResponse
from django.shortcuts import redirect, get_object_or_404
from django.utils import timezone
from django.views.decorators.csrf import csrf_exempt
from dotenv import load_dotenv
from openai import OpenAI
import openpyxl
from openpyxl.utils import get_column_letter
from django.http import HttpResponse
from .models import ChatErrorLog

from chatbot_app.models import ChatMessage, ChatSession, ChatErrorLog, TokenUsage

load_dotenv()


client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))


# Decorador para requerir tokens
def require_tokens(func):
    def wrapper(request, *args, **kwargs):
        token_record = get_or_create_token_record(request)
        if not token_record.can_use_api():
            return JsonResponse({
                'error': 'Se alcanzó el límite mensual de tokens. Espera al siguiente ciclo.'
            }, status=403)
        return func(request, token_record=token_record, *args, **kwargs)
    return wrapper


@csrf_exempt
@require_tokens
def send_message(request, token_record):
    if request.method != 'POST':
        return JsonResponse({'error': 'Método no permitido'}, status=405)

    try:
        data = json.loads(request.body)
        user_message = data.get('user_message', '').strip()
        if not user_message:
            return JsonResponse({'error': 'El mensaje está vacío'}, status=400)

        # Llamada a OpenAI
        response = client.chat.completions.create(
            model="gpt-5-nano",
            messages=[{"role": "user", "content": user_message}]
        )

        bot_response = response.choices[0].message.content

        # Guardar mensaje
        ChatMessage.objects.create(
            user_message=user_message,
            bot_response=bot_response
        )

        # Registrar tokens
        usage = getattr(response, 'usage', None)
        if usage:
            token_record.input_tokens += getattr(usage, 'prompt_tokens', 0)
            token_record.output_tokens += getattr(usage, 'completion_tokens', 0)
            token_record.save()

        return JsonResponse({
            'bot_response': bot_response,
            'tokens_restantes': {
                'entrada': token_record.remaining_input(),
                'salida': token_record.remaining_output(),
                'memoria': token_record.remaining_memory()
            }
        })

    except Exception as e:
        traceback.print_exc()
        return JsonResponse({'error': str(e)}, status=500)


@csrf_exempt
def start_chat(request):
    # Crear una nueva sesión
    session = ChatSession.objects.create(user=request.user if request.user.is_authenticated else None)
    return redirect('chat', session_id=session.id)


@csrf_exempt
def chat(request, session_id):
    session = get_object_or_404(ChatSession, id=session_id)
    messages = list(session.messages.values(
        'id', 'user_message', 'bot_response', 'created_at'
    ))
    return JsonResponse({
        'session_id': session.id,
        'messages': messages
    })


'''
@csrf_exempt
def send_message_ajax(request, session_id):
    if request.method == 'POST':
        try:
            data = json.loads(request.body)
            user_message = data.get('message')

            if not user_message:
                return JsonResponse({'error': 'No se recibió mensaje'}, status=400)

            response = client.chat.completions.create(
                model="gpt-5-nano",
                messages=[{"role": "user", "content": user_message}],
            )

            bot_response = response.choices[0].message.content

            session = get_object_or_404(ChatSession, id=session_id)

            chat_message = ChatMessage.objects.create(
                session=session,
                user_message=user_message,
                bot_response=bot_response,
                created_at=timezone.now()
            )

            return JsonResponse({
                'reply': bot_response,
                'user_message': user_message,
                'created_at': chat_message.created_at.strftime("%d %b %Y %H:%M")
            })

        except Exception as e:
            return JsonResponse({'error': str(e)}, status=500)

    return JsonResponse({'error': 'Método no permitido'}, status=405)
'''


@csrf_exempt
@require_tokens
def send_message_ajax(request, session_id, token_record):
    if request.method != "POST":
        return JsonResponse({'error': 'Método no permitido'}, status=405)

    try:
        data = json.loads(request.body)
        user_message = data.get('message')
        if not user_message:
            return JsonResponse({'error': 'No se recibió mensaje'}, status=400)

        session = get_object_or_404(ChatSession, id=session_id)

        # Llamada a OpenAI
        response = client.chat.completions.create(
            model="gpt-5-nano",
            messages=[{"role": "user", "content": user_message}]
        )

        bot_response = response.choices[0].message.content

        chat_message = ChatMessage.objects.create(
            session=session,
            user_message=user_message,
            bot_response=bot_response
        )

        # Registrar tokens
        usage = getattr(response, 'usage', None)
        if usage:
            token_record.input_tokens += getattr(usage, 'prompt_tokens', 0)
            token_record.output_tokens += getattr(usage, 'completion_tokens', 0)
            token_record.save()

        return JsonResponse({
            'reply': bot_response,
            'user_message': user_message,
            'created_at': chat_message.created_at.strftime("%d %b %Y %H:%M"),
            'tokens_restantes': {
                'entrada': token_record.remaining_input(),
                'salida': token_record.remaining_output(),
                'memoria': token_record.remaining_memory()
            }
        })

    except Exception as e:
        traceback.print_exc()
        return JsonResponse({'error': str(e)}, status=500)


@csrf_exempt
def log_error(request):
    if request.method == "POST":
        data = json.loads(request.body)
        ChatErrorLog.objects.create(
            user=request.user if request.user.is_authenticated else None,
            session_id=data.get("session_id"),
            user_message=data.get("user_message"),
            error_code=data.get("error_code"),
            error_text=data.get("error_text")
        )
        return JsonResponse({"status": "ok"})
    return JsonResponse({"error": "Método no permitido"}, status=405)


def ver_errores(request):
    errores = ChatErrorLog.objects.all().order_by('-created_at').values(
        'id', 'user__username', 'session_id', 'user_message', 'error_text', 'error_code', 'created_at'
    )
    return JsonResponse(list(errores), safe=False)


def exportar_errores_excel(request):
    # Obtener los errores
    errores = ChatErrorLog.objects.all().order_by('-created_at').values(
        'id', 'user__username', 'session_id', 'user_message', 'error_text', 'error_code', 'created_at'
    )

    # Crear un libro y una hoja
    wb = openpyxl.Workbook()
    ws = wb.active
    ws.title = "Errores API"

    # Encabezados
    headers = ['ID', 'Usuario', 'Session ID', 'Mensaje', 'Error', 'Código', 'Fecha']
    ws.append(headers)

    # Agregar los datos
    for err in errores:
        ws.append([
            err['id'],
            err['user__username'] or 'Anon',
            err['session_id'] or '-',
            err['user_message'],
            err['error_text'],
            err['error_code'] or '-',
            err['created_at'].strftime("%Y-%m-%d %H:%M:%S")
        ])

    # Ajustar ancho de columnas automáticamente
    for col in ws.columns:
        max_length = max(len(str(cell.value)) for cell in col)
        ws.column_dimensions[get_column_letter(col[0].column)].width = max_length + 2

    # Preparar respuesta HTTP
    response = HttpResponse(content_type='application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
    response['Content-Disposition'] = 'attachment; filename=errores_api.xlsx'
    wb.save(response)
    return response


@csrf_exempt
@require_tokens
def mejorar_descripcion_puesto(request, token_record):
    if request.method != 'POST':
        return JsonResponse({'error': 'Método no permitido'}, status=405)

    try:
        print("\n[API] Llegó solicitud a /api/mejorar-descripcion/")
        print("Cuerpo recibido bruto:", request.body)

        data = json.loads(request.body)
        print("JSON parseado correctamente:", data)

        candidato_info = data.get('candidato', {})
        puesto_actual = data.get('puesto_actual', '')

        print("Candidato recibido:", candidato_info)
        print("Puesto actual recibido:", puesto_actual)

        if not puesto_actual or not candidato_info:
            print("Datos incompletos: falta candidato o puesto_actual")
            return JsonResponse({'error': 'Datos incompletos (faltan campos)'}, status=400)

        # Crear prompt
        prompt = f"""
        Mejora la descripción del puesto "{puesto_actual}" para el candidato {candidato_info.get('nombre', '')}.

        Proporciona:
        1. Descripción mejorada y atractiva
        2. Responsabilidades clave
        3. Requisitos deseables
        4. Beneficios destacados

        Hazlo persuasivo y profesional.
        """
        print("Prompt generado correctamente:\n", prompt)

        # Llamada a OpenAI
        response = client.chat.completions.create(
            model="gpt-5-nano",
            messages=[{"role": "user", "content": prompt}]
        )
        print("Respuesta recibida del modelo IA:", response)

        descripcion_mejorada = response.choices[0].message.content
        print("Descripción mejorada generada correctamente")

        # Registrar tokens
        usage = getattr(response, 'usage', None)
        if usage:
            token_record.input_tokens += getattr(usage, 'prompt_tokens', 0)
            token_record.output_tokens += getattr(usage, 'completion_tokens', 0)
            token_record.save()

        return JsonResponse({
            'descripcion_mejorada': descripcion_mejorada,
            'puesto_original': puesto_actual,
            'candidato': candidato_info.get('nombre', '')
        })

    except Exception as e:
        print("ERROR INTERNO EN mejorar_descripcion_puesto:", str(e))
        traceback.print_exc()
        return JsonResponse({'error': str(e)}, status=500)


def token_usage_list(request):
    """
    Lista todos los registros de tokens del mes actual.
    Para administradores o panel general.
    """
    now = timezone.now()
    month_start = now.replace(day=1)
    tokens = TokenUsage.objects.filter(month=month_start).select_related('user')

    # Devolver JSON simple
    data = [
        {
            'user': t.user.username if t.user else 'Anon',
            'input_tokens': t.input_tokens,
            'output_tokens': t.output_tokens,
            'memory_tokens': t.memory_tokens,
            'remaining_input': t.remaining_input(),
            'remaining_output': t.remaining_output(),
            'remaining_memory': t.remaining_memory()
        }
        for t in tokens
    ]

    # 🔍 Mostrar en la consola del servidor Django
    print("[API] Token usage data:")
    for d in data:
        print(d)

    return JsonResponse({'data': data})


def token_usage_detail(request, user_id):
    """
    Ver los tokens de un usuario específico del mes actual.
    """
    now = timezone.now()
    month_start = now.replace(day=1)
    user = get_object_or_404(User, id=user_id)
    token_record = TokenUsage.objects.filter(user=user, month=month_start).first()

    if not token_record:
        return JsonResponse({'error': 'No se encontraron registros de tokens para este usuario'}, status=404)

    data = {
        'user': user.username,
        'input_tokens': token_record.input_tokens,
        'output_tokens': token_record.output_tokens,
        'memory_tokens': token_record.memory_tokens,
        'remaining_input': token_record.remaining_input(),
        'remaining_output': token_record.remaining_output(),
        'remaining_memory': token_record.remaining_memory()
    }
    return JsonResponse(data)


def get_or_create_token_record(request):
    """
    Retorna el registro de tokens basado en el usuario o sesión anónima.
    """
    now = timezone.now().date()
    month_start = now.replace(day=1)

    # Si el usuario está autenticado, usar usuario, sino sesión
    user = request.user if request.user.is_authenticated else None
    session_key = request.session.session_key if hasattr(request, 'session') else None

    if user:
        token_record, created = TokenUsage.objects.get_or_create(
            user=user,
            month=month_start,
            defaults={
                'input_tokens': 0,
                'output_tokens': 0,
                'memory_tokens': 0,
                'notified_limit': False
            }
        )
    else:
        # Para usuarios anónimos, usar una combinación de session_key o IP
        session_id = session_key or f"anon_{request.META.get('REMOTE_ADDR', 'unknown')}"

        token_record, created = TokenUsage.objects.get_or_create(
            user=None,
            session_id=session_id,
            month=month_start,
            defaults={
                'input_tokens': 0,
                'output_tokens': 0,
                'memory_tokens': 0,
                'notified_limit': False
            }
        )

    return token_record


@csrf_exempt
def check_token_status(request):
    """
    Vista para que el frontend verifique el estado de los tokens
    """
    token_record = get_or_create_token_record(request)

    # Calcular próximo ciclo
    next_month = token_record.month.month % 12 + 1
    next_year = token_record.month.year + (1 if next_month == 1 else 0)
    next_cycle = timezone.datetime(next_year, next_month, 1).date()

    return JsonResponse({
        'can_use_api': token_record.can_use_api(),
        'is_blocked': token_record.is_blocked(),
        'input_tokens': token_record.input_tokens,
        'output_tokens': token_record.output_tokens,
        'remaining_input': token_record.remaining_input(),
        'remaining_output': token_record.remaining_output(),
        'max_tokens': token_record.MAX_TOKENS,
        'notified_limit': token_record.notified_limit,
        'current_cycle': token_record.month.strftime("%Y-%m"),
        'next_reset_date': next_cycle.strftime("%Y-%m-%d")
    })

