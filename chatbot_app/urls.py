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
    # Estado de tokens
    path("api/tokens/status/", views.check_token_status, name="check_token_status"),
    # Cambiar modelo IA
    path("api/model/set/", views.admin_set_model, name="admin_set_model"),
    # Crear template nuevo
    path("api/prompt/create/", views.admin_create_prompt, name="admin_create_prompt"),
    # Activar template específico
    path("api/prompt/activate/", views.admin_activate_prompt, name="admin_activate_prompt"),
    # Publicar revisión final
    path("api/revision/publicar/", views.admin_publicar_revision, name="admin_publicar_revision"),
    path("api/prompt/list/", views.prompt_list_view, name="prompt_list"),
    path("api/revisions/list/", views.revisions_list_view, name="revisions_list"),
    # Los informes generados de SAP SuccessFactors (SSFF)
    path('sap/importar-puesto/', views.import_puesto_sap, name="import_puesto_sap"),
    path('sap/puestos/', views.listar_puestos, name="listar_puestos"),
    path('sap/importar-puesto-csv/', views.importar_puestos_csv, name="importar_puestos_csv"),
    path('sap/importar-puestos-urls/', views.importar_puestos_desde_urls, name="importar_puestos_desde_urls"),
    # Informe manual
    path('perfiles/crear/', views.crear_perfil_manual, name='crear_perfil_manual'),
    path('perfiles/listar/', views.lista_perfiles, name='lista_perfiles'),
    path("perfil/<int:id>/", views.ver_perfil, name="ver_perfil"),
    # COMPARADOR
    # path("comparar-cv/", views.comparar_cv_view, name="comparar_cv"),
    path("api/comparar/<int:candidato_id>/<int:req_id>/", views.comparar_candidato_view, name="comparar_candidato"),
    # MEJORAR PUESTO DESDE INFORME SAP SSFF
    path("api/mejorar-puesto/<str:req_id>/", views.mejorar_puesto_view, name="mejorar_puesto"),
    # COMPARATIVA MANUAL
    path("api/comparar_manual/<int:candidato_id>/", views.comparar_candidato_manual_view, name="comparativa_manual"),
    # EXTRAER PUESTO DESDE CV
    path('api/extraer_puesto_candidato/<int:candidato_id>/', views.extraer_puesto_candidato, name='extraer_puesto_candidato'),
    path('api/comparar_manual/<int:candidato_id>/', views.comparar_candidato_manual_view, name='comparativa_manual'),
]
