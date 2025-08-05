<?php
require '../vendor/autoload.php';
include 'conexion_bd.php';

if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
    http_response_code(200);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

\Stripe\Stripe::setApiKey('sk_test_51Rq...');//private key de stripe

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? null;

if (!$sig_header) {
    http_response_code(400);
    exit('Missing Stripe signature');
}

$endpoint_secret = 'whsec_i0...';//webhook de stripe

try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
    );
} catch(\UnexpectedValueException $e) {
    http_response_code(400);
    exit();
} catch(\Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit();
}

switch ($event->type) {
    case 'customer.subscription.updated':
    case 'customer.subscription.created':
    case 'customer.subscription.deleted':
        $subscription = $event->data->object;

        $stripe_subscription_id = $subscription->id;
        $status = $subscription->status;
        $new_price_id = $subscription->items->data[0]->price->id ?? null;
        $fecha_actualizacion = date('Y-m-d H:i:s');

        // Busca el id_susc en tabla suscripcion usando el price_id
        $stmt_susc = $conexion->prepare("SELECT id_susc FROM suscripcion WHERE stripe_price_id = ?");
        $stmt_susc->bind_param("s", $new_price_id);
        $stmt_susc->execute();
        $result_susc = $stmt_susc->get_result();
        $id_susc = null;
        if ($row = $result_susc->fetch_assoc()) {
            $id_susc = $row['id_susc'];
        }
        $stmt_susc->close();

        if (!$id_susc) {
            // Si no existe el plan en la tabla suscripcion, aborta o maneja error
            http_response_code(400);
            exit("Plan no registrado en la base de datos");
        }

        // Obtener estado y plan actual guardados
        $stmt_select = $conexion->prepare("SELECT plan_id, estado FROM historial WHERE stripe_subscription_id = ?");
        $stmt_select->bind_param("s", $stripe_subscription_id);
        $stmt_select->execute();
        $stmt_select->bind_result($old_plan_id, $old_status);
        $stmt_select->fetch();
        $stmt_select->close();

        $actualizar_plan = ($old_plan_id !== $new_price_id);
        $actualizar_estado = ($old_status !== $status);

        if ($actualizar_plan) {
            // Si cambió el plan, actualizar plan, estado, fecha y Suscripcion_id_susc
            $stmt_update = $conexion->prepare("UPDATE historial SET estado = ?, plan_id = ?, updated_at = ?, Suscripcion_id_susc = ? WHERE stripe_subscription_id = ?");
            $stmt_update->bind_param("sssis", $status, $new_price_id, $fecha_actualizacion, $id_susc, $stripe_subscription_id);
            $stmt_update->execute();
            $stmt_update->close();
        } elseif ($actualizar_estado) {
            // Si solo cambió el estado, actualizar estado y fecha
            $stmt_update = $conexion->prepare("UPDATE historial SET estado = ?, updated_at = ? WHERE stripe_subscription_id = ?");
            $stmt_update->bind_param("sss", $status, $fecha_actualizacion, $stripe_subscription_id);
            $stmt_update->execute();
            $stmt_update->close();
        }
       
        break;

    default:
       
        break;
}

http_response_code(200);
