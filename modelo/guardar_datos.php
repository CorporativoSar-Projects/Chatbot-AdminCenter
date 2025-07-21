<?php
session_start();
require_once 'conexion_bd.php';

//Indica que se recibira la respuesta en formato JSON
header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);


//Verifica que se hayan enviado los campos 
if (!isset($input['seccion']) || !isset($input['datos'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

//Se recuperan los datos de las paginas 
$seccion = $input['seccion'];
$datos = $input['datos'];
$id_adm = $_SESSION['id_adm'] ?? null;
$id_tipo_chatbot = 1;

//Se obtiene el id del chatbot 
$id_chatbot = $datos['id_chatbot'] ?? null;


if ($seccion === 'estilo') {
    if (!$id_chatbot) {
        // INSERTAR nuevo chatbot si no existe un id 
        $sql = "INSERT INTO chatbot (
            inp_nombre, colorPrimario, colorSecundario, colorTexto,
            colorAcento, colorRespuestaUsuario, urlLogotipo,
            Tipo_Chatbot_idTipo_Chatbot, Administrador_id_adm
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param(
            "ssssssssi",
            $datos['inp_nombre'],
            $datos['colorPrimario'],
            $datos['colorSecundario'],
            $datos['colorTexto'],
            $datos['colorAcento'],
            $datos['colorUsuario'],
            $datos['urlLogotipo'],
            $id_tipo_chatbot,
            $id_adm
        );

        if ($stmt->execute()) {
            $nuevoID = $stmt->insert_id; //id creado 
            $_SESSION['id_chatbot'] = $nuevoID; 
            echo json_encode(['success' => true, 'id_chatbot' => $nuevoID]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        $stmt->close();
        $conexion->close();
        exit;
    } else {
        // Actualizar estilo si ya existe id chatbot
        $sql = "UPDATE chatbot SET
            inp_nombre = ?, colorPrimario = ?, colorSecundario = ?, colorTexto = ?,
            colorAcento = ?, colorRespuestaUsuario = ?, urlLogotipo = ?
            WHERE id_chatbot = ?";

        $stmt = $conexion->prepare($sql);
        $stmt->bind_param(
            "sssssssi",
            $datos['inp_nombre'],
            $datos['colorPrimario'],
            $datos['colorSecundario'],
            $datos['colorTexto'],
            $datos['colorAcento'],
            $datos['colorUsuario'],
            $datos['urlLogotipo'],
            $id_chatbot
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Estilo actualizado', 'id_chatbot' => $id_chatbot]);
        } else {
            echo json_encode(['success' => false, 'error' => $stmt->error]);
        }

        $stmt->close();
        $conexion->close();
        exit;
    }
}

// Se valida el id, para las demás secciones
if (!$id_chatbot) {
    echo json_encode(['success' => false, 'error' => 'Falta id_chatbot']);
    exit;
}

$campos = '';
$valores = [];
$tipos = '';

//Se utilizo un switch para poder gestionar las multiples secciones 
switch ($seccion) {
    case 'burbuja':
        $stmt = $conexion->prepare("UPDATE chatbot SET inp_burbuja = ? WHERE id_chatbot = ?");
        $stmt->bind_param("si", $datos['inp_burbuja'], $id_chatbot);
        break;

    case 'mensaje_inicial':
        $stmt = $conexion->prepare("UPDATE chatbot SET inp_saludo = ?, inp_conversa1 = ?, inp_conversa2 = ?, inp_conversa3 = ? WHERE id_chatbot = ?");
        $stmt->bind_param("ssssi", $datos['inp_saludo'], $datos['inp_conversa1'], $datos['inp_conversa2'], $datos['inp_conversa3'], $id_chatbot);
        break;

    case 'conversacion':
        $stmt = $conexion->prepare("UPDATE chatbot SET 
            inp_mensaje_usuario = ?, inp_columna = ?, inp_url_informe = ?, 
            inp_mensaje_usuario2 = ?, inp_columna2 = ?, 
            inp_mensaje_usuario3 = ?, inp_columna3 = ?, inp_url_informe3 = ? 
            WHERE id_chatbot = ?");
        $stmt->bind_param("ssssssssi", 
            $datos['inp_mensaje_usuario'], 
            $datos['inp_columna'], 
            $datos['inp_url_informe'], 
            $datos['inp_mensaje_usuario2'], 
            $datos['inp_columna2'], 
            $datos['inp_mensaje_usuario3'], 
            $datos['inp_columna3'], 
            $datos['inp_url_informe3'], 
            $id_chatbot
        );
        break;

    case 'despedida':
        $stmt = $conexion->prepare("UPDATE chatbot SET inp_despedida = ? WHERE id_chatbot = ?");
        $stmt->bind_param("si", $datos['inp_despedida'], $id_chatbot);
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Sección no válida']);
        exit;
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
?>


