const API_BASE = "http://localhost:8000/api";

/**
 * Obtiene la lista de todos los registros de uso de tokens
 * (para el panel general de administración)
 */
async function obtenerListaTokens() {
  try {
    const res = await fetch(`${API_BASE}/tokens/`);
    if (!res.ok) throw new Error(await res.text());

    const data = await res.json();
    console.log("Lista completa de tokens:", data);
    return data;
  } catch (error) {
    console.error("❌ Error obteniendo lista de tokens:", error);
  }
}

/**
 * Obtiene el detalle de uso de tokens de un usuario específico
 * @param {number} userId - ID del usuario
 */
async function obtenerDetalleTokens(userId) {
  try {
    const res = await fetch(`${API_BASE}/tokens/${userId}/`);
    if (!res.ok) throw new Error(await res.text());

    const data = await res.json();
    console.log(`✅ Detalle de tokens del usuario ${userId}:`, data);
    return data;
  } catch (error) {
    console.error(`❌ Error obteniendo detalle de tokens del usuario ${userId}:`, error);
  }
}

/**
 * Simula una notificación cuando el usuario supera el límite de tokens
 * (en este punto, el backend debería enviar el estado, pero aquí lo simulamos)
 */
function verificarLimiteTokens(tokenData) {
  const limite = 1000000; // 1 millón de tokens
  const total = tokenData.tokens_usados || 0;

  if (total >= limite) {
    console.warn("⚠️ Límite de tokens alcanzado. El sistema debería bloquear nuevas solicitudes.");
    alert("Has alcanzado tu límite mensual de tokens. No puedes usar la IA hasta el siguiente ciclo.");
    return false;
  }
  console.log("🟢 Tokens dentro del límite:", total);
  return true;
}

/**
 * Simula el reseteo automático de tokens en un nuevo ciclo mensual
 * (esto normalmente lo haría el backend mediante una tarea programada)
 */
function resetearContadorTokens() {
  console.log("♻️ Ciclo mensual iniciado. Los contadores de tokens se han reseteado.");
  alert("Nuevo ciclo mensual iniciado. Los tokens fueron reseteados.");
}





