<?php
session_start();
require '../vendor/autoload.php';
include 'conexion_bd.php';

\Stripe\Stripe::setApiKey('...');//private key de stripe

$data = json_decode(file_get_contents('php://input'), true);

// Generar token y ID de empresa
$token = bin2hex(random_bytes(16));
$data['id_emp'] = generarIdEmp($data['nombre_emp']);
$datos_json = json_encode($data);


$stmt = $conexion->prepare("INSERT INTO registro_tmp (token, datos) VALUES (?, ?)");
$stmt->bind_param("ss", $token, $datos_json);
$stmt->execute();
$stmt->close();


$_SESSION['registro'] = $data;

//plan a price_id de Stripe
$precios = [
  "basicoMensual" => "price_1S2Q1XI0PxzL8sHBjM4Xs5fp",
  "basicoAnual" => "price_1S2bxZI0PxzL8sHBAm7bfbDR",
  "prueba" => "price_1Rw9Nt75hyzhrDdqVfqMus5Y"
];

$priceId = $precios[$data['nombre_susc']] ?? null;

if (!$priceId) {
  echo json_encode(['error' => 'Plan no válido']);
  exit;
}

try {
  $session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'mode' => 'subscription',
    'line_items' => [[
      'price' => $priceId,
      'quantity' => 1,
    ]],
    'client_reference_id' => $token,
    'success_url' => 'http://localhost/Chatbot-AdminCenter/modelo/registro_exito.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'http://localhost/Chatbot-AdminCenter/registerForm.php',
  ]);

  echo json_encode(['linkPago_susc' => $session->url]);
} catch (Exception $e) {
  echo json_encode(['error' => $e->getMessage()]);
}

function generarIdEmp($nombre){
  $base = substr(preg_replace('/[^a-zA-Z0-9]/', '', strtolower($nombre)), 0, 5);
  $sufijo = substr(time(), -4) . substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 2);
  return $base . $sufijo;
}
?>
