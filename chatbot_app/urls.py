from django.urls import path
from .views import send_message, send_message_ajax # list_messages
from . import views

urlpatterns = [
    path('api/send', send_message, name='send_message'),
    # path('api/lista', list_messages, name='list_messages'),
    path('send_ajax/', send_message_ajax, name='send_message_ajax'),
    path('api/start/', views.start_chat, name='start_chat'),
    path('api/chat/<int:session_id>/', views.chat, name='chat'),
    path('api/chat/<int:session_id>/send/', views.send_message_ajax, name='send_message_ajax'),
    path('api/log_error/', views.log_error, name='log_error'),
    path('api/errores/', views.ver_errores, name='ver_errores'),
    path('api/mejorar-descripcion/', views.mejorar_descripcion_puesto, name='mejorar_descripcion'),
    path('api/tokens/', views.token_usage_list, name='token_usage_list'),
    path('api/tokens/<int:user_id>/', views.token_usage_detail, name='token_usage_detail'),
    path('exportar-errores/', views.exportar_errores_excel, name='exportar_errores_excel'),
]
