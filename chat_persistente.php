<?php
// chat_persistente.php - Sistema de chat persistente

class ChatPersistente {
    private static $instancia = null;
    private $userId;
    
    private function __construct($userId) {
        $this->userId = $userId;
    }
    
    public static function obtenerInstancia($userId = null) {
        if (self::$instancia === null && $userId !== null) {
            self::$instancia = new self($userId);
        }
        return self::$instancia;
    }
    
    public function obtenerChat() {
        // Usar almacenamiento en archivo temporal para persistencia real
        $archivoChat = $this->obtenerRutaArchivo();
        
        if (file_exists($archivoChat)) {
            $contenido = file_get_contents($archivoChat);
            $chat = unserialize($contenido);
            
            if (is_array($chat)) {
                return $chat;
            }
        }
        
        // Si no existe, crear chat inicial
        return $this->crearChatInicial();
    }
    
    public function agregarMensaje($tipo, $mensaje, $html = null) {
        $chat = $this->obtenerChat();
        
        $chat[] = [
            'tipo' => $tipo,
            'mensaje' => $mensaje,
            'html' => $html ?: $mensaje,
            'timestamp' => time()
        ];
        
        // Limitar a 50 mensajes
        if (count($chat) > 50) {
            $chat = array_slice($chat, -50);
        }
        
        // Guardar en archivo
        $this->guardarChat($chat);
        
        return true;
    }
    
    public function limpiarChat() {
        $chatInicial = $this->crearChatInicial();
        $this->guardarChat($chatInicial);
        return true;
    }
    
    private function crearChatInicial() {
        return [
            [
                'tipo' => 'bot',
                'mensaje' => '¡Hola! Soy tu asistente de IA. ¿En qué puedo ayudarte hoy?',
                'timestamp' => time(),
                'html' => '¡Hola! Soy tu asistente de IA. ¿En qué puedo ayudarte hoy?<div class="special-buttons">
                    <button class="special-btn" onclick="activarComparacionCV()">🔍 Análisis de candidatos</button>
                    <button class="special-btn" onclick="mostrarOpcionesMejoraDescripcion()">✏️ Mejorar descripción de puesto</button>
                    <button class="special-btn" onclick="procesarSAPSSFF()">📊 Procesar SAP SSFF</button>
                </div>'
            ]
        ];
    }
    
    private function obtenerRutaArchivo() {
        // Crear directorio para chats si no existe
        $directorio = __DIR__ . '/chats_temp/';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }
        
        // Nombre del archivo basado en usuario y sesión
        $sessionId = session_id();
        return $directorio . 'chat_' . $this->userId . '_' . md5($sessionId) . '.dat';
    }
    
    private function guardarChat($chat) {
        $archivoChat = $this->obtenerRutaArchivo();
        file_put_contents($archivoChat, serialize($chat));
    }
    
    public function eliminarArchivoChat() {
        $archivoChat = $this->obtenerRutaArchivo();
        if (file_exists($archivoChat)) {
            unlink($archivoChat);
        }
    }
}
?>

