<?php
session_start();
require_once 'conexion_bd.php';
header('Content-Type: application/json');

$id_adm = $_SESSION['id_adm'] ?? null;

if (!$id_adm) {
    echo json_encode(['success' => false, 'error' => 'No hay sesión activa']);
    exit;
}

// Leer datos JSON enviados desde JS
$input = json_decode(file_get_contents("php://input"), true);
$datos = $input ?? [];

$id_chatbot = isset($datos['id_chatbot']) ? intval($datos['id_chatbot']) : null;
$id_tipo_chatbot = 1;

// Campos que puede tener el chatbot
$campos = [
    'inp_nombre','colorPrimario','colorSecundario','colorTexto','colorAcento','colorRespuestaUsuario',
    'urlLogotipo','inp_burbuja','inp_saludo','inp_conversa1','inp_conversa2','inp_conversa3',
    'inp_mensaje_usuario','inp_columna','inp_url_informe','inp_mensaje_usuario2','inp_columna2',
    'inp_mensaje_usuario3','inp_columna3','inp_url_informe3','inp_despedida'
];

// ----------------- INSERT -----------------
if (!$id_chatbot) {
    $sql = "INSERT INTO chatbot (
        " . implode(",", $campos) . ",
        Tipo_Chatbot_idTipo_Chatbot, Administrador_id_adm
    ) VALUES (" . str_repeat("?,", count($campos)) . "?,?)";

    $stmt = $conexion->prepare($sql);
    $tipos = str_repeat("s", count($campos)) . "ii";

    $valores = [];
    foreach ($campos as $campo) {
        $valores[] = $datos[$campo] ?? '';
    }
    $valores[] = $id_tipo_chatbot;
    $valores[] = $id_adm;

    $stmt->bind_param($tipos, ...$valores);

    if ($stmt->execute()) {
        $_SESSION['id_chatbot'] = $stmt->insert_id;

        // Generar JSON al crear
        ob_start();
        include "generar_json.php";
        $json_output = ob_get_clean();

        echo json_encode([
            'success' => true,
            'message' => 'Chatbot creado correctamente',
            'id_chatbot' => $_SESSION['id_chatbot'],
            'json' => json_decode($json_output, true)
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
    $conexion->close();
    exit;
}

// ----------------- UPDATE -----------------

// Obtener datos actuales
$query = $conexion->prepare("SELECT * FROM chatbot WHERE id_chatbot = ?");
$query->bind_param("i", $id_chatbot);
$query->execute();
$result = $query->get_result();
$actual = $result->fetch_assoc();
$query->close();

if (!$actual) {
    echo json_encode(['success' => false, 'error' => 'Chatbot no encontrado']);
    exit;
}

// Mezclar datos: los nuevos reemplazan los viejos si existen
$nuevos = $actual;
foreach ($campos as $campo) {
    if (isset($datos[$campo]) && $datos[$campo] !== null && $datos[$campo] !== '') {
        $nuevos[$campo] = $datos[$campo];
    }
}

// Construir UPDATE dinámico
$set = [];
$valores = [];
foreach ($campos as $campo) {
    $set[] = "$campo = ?";
    $valores[] = $nuevos[$campo];
}
$valores[] = $id_chatbot;
$tipos = str_repeat("s", count($campos)) . "i";

$sql = "UPDATE chatbot SET " . implode(", ", $set) . " WHERE id_chatbot = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param($tipos, ...$valores);

if ($stmt->execute()) {
    // Generar JSON al actualizar
    ob_start();
    include "generar_json.php";
    $json_output = ob_get_clean();

    echo json_encode([
        'success' => true,
        'message' => 'Chatbot actualizado correctamente',
        'json' => json_decode($json_output, true)
    ]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conexion->close();
?>
