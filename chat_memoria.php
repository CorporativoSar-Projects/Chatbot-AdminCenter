<?php
// chat_memoria.php - VERSIÓN CORREGIDA

class ChatMemoria {
    
    /**
     * Obtener o crear el chat de un usuario desde SESSION
     */
    public static function obtenerChat($userId) {
        // Asegurar que la sesión esté iniciada PERO NO iniciarla aquí
        if (session_status() == PHP_SESSION_NONE) {
            // Si la sesión no está iniciada, no podemos obtener el chat
            // Dejar que el archivo principal maneje la sesión
            return self::crearChatInicial();
        }
        
        $sessionKey = 'chat_user_' . $userId;
        
        if (!isset($_SESSION[$sessionKey]) || empty($_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey] = self::crearChatInicial();
        }
        
        return $_SESSION[$sessionKey];
    }
    
    /**
     * Crear el chat inicial
     */
    private static function crearChatInicial() {
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
    
    /**
     * Agregar un mensaje al chat en SESSION
     */
    public static function agregarMensaje($userId, $tipo, $mensaje, $html = null) {
        // Asegurar que la sesión esté iniciada
        if (session_status() == PHP_SESSION_NONE) {
            return false; // No podemos agregar sin sesión
        }
        
        $sessionKey = 'chat_user_' . $userId;
        
        // Obtener chat existente o crear uno nuevo
        if (!isset($_SESSION[$sessionKey])) {
            $_SESSION[$sessionKey] = self::crearChatInicial();
        }
        
        $chat = $_SESSION[$sessionKey];
        
        $chat[] = [
            'tipo' => $tipo,
            'mensaje' => $mensaje,
            'html' => $html ?: $mensaje,
            'timestamp' => time()
        ];
        
        // Limitar a 50 mensajes máximo por usuario
        if (count($chat) > 50) {
            $chat = array_slice($chat, -50);
        }
        
        $_SESSION[$sessionKey] = $chat;
        return true;
    }
    
    /**
     * Limpiar el chat de un usuario (excepto el primer mensaje)
     */
    public static function limpiarChat($userId) {
        if (session_status() == PHP_SESSION_NONE) {
            return false;
        }
        
        $sessionKey = 'chat_user_' . $userId;
        if (isset($_SESSION[$sessionKey]) && count($_SESSION[$sessionKey]) > 0) {
            $primerMensaje = $_SESSION[$sessionKey][0];
            $_SESSION[$sessionKey] = [$primerMensaje];
        }
        return true;
    }
}
?>