<?php
require '../vendor/autoload.php';
include 'conexion_bd.php';
session_start();

// Validar sesión
if (!isset($_SESSION['id_adm'], $_SESSION['id_emp'], $_SESSION['correo_adm'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Usuario no autenticado o datos de empresa faltantes']);
    exit;
}

\Stripe\Stripe::setApiKey('...');//llave secreta de stripe

// Recibir POST JSON
$input = json_decode(file_get_contents('php://input'), true);
$nombre_susc = $input['nombre_susc'] ?? '';

if (!$nombre_susc) {
    http_response_code(400);
    echo json_encode(['error' => 'No se proporcionó un plan']);
    exit;
}

// Obtener plan de Bd
$stmt = $conexion->prepare("SELECT id_susc, stripe_price_id FROM suscripcion WHERE nombre_susc = ? LIMIT 1");
$stmt->bind_param("s", $nombre_susc);
$stmt->execute();
$plan = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$plan) {
    http_response_code(404);
    echo json_encode(['error' => 'Plan no encontrado']);
    exit;
}

$id_susc = $plan['id_susc'];
$stripe_price_id = $plan['stripe_price_id'];
$id_emp = $_SESSION['id_emp'];
$correo_adm = $_SESSION['correo_adm'];


try {
    $checkout_session = \Stripe\Checkout\Session::create([
        'mode' => 'subscription',
        'line_items' => [[
            'price' => $stripe_price_id,
            'quantity' => 1,
        ]],
        'customer_email' => $correo_adm,
        'success_url' => 'http://localhost/Chatbot-AdminCenter/menu.php?success=1',
        'cancel_url'  => 'http://localhost/Chatbot-AdminCenter/menu.php?canceled=1',
        'subscription_data' => [
            'metadata' => [
                'Empresa_id_emp' => $id_emp,
                'id_susc' => $id_susc
            ]
        ]
    ]);

    echo json_encode(['url' => $checkout_session->url]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
