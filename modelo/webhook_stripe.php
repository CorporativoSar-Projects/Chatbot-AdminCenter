<?php
require '../vendor/autoload.php';
include 'conexion_bd.php';

\Stripe\Stripe::setApiKey('LLave secreta de stripe');

$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? null;
$endpoint_secret = ''; //codigo del webhook de stripe

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
} catch (\UnexpectedValueException | \Stripe\Exception\SignatureVerificationException $e) {
    http_response_code(400);
    exit();
}

// Solo manejar eventos de suscripción
if (!in_array($event->type, ['customer.subscription.created','customer.subscription.updated','customer.subscription.deleted'])) {
    http_response_code(200);
    exit();
}

$subscription = $event->data->object;
$stripe_subscription_id = $subscription->id;
$customer_id = $subscription->customer;
$status = $subscription->status;
$new_price_id = $subscription->items->data[0]->price->id ?? null;
$fecha_actualizacion = date('Y-m-d H:i:s');

// Inicializar variables
$id_emp = $subscription->metadata->Empresa_id_emp ?? null;
$id_susc = $subscription->metadata->id_susc ?? null;
$nombre_susc = null;
$precio_susc = 0;

// Si no hay metadata, obtener Empresa_id_emp de historial previo
if (!$id_emp) {
    $stmt = $conexion->prepare("
        SELECT Empresa_id_emp 
        FROM historial 
        WHERE stripe_customer_id = ? 
        ORDER BY fecha_contratacion DESC 
        LIMIT 1
    ");
    $stmt->bind_param("s", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $id_emp = $row['Empresa_id_emp'] ?? null;
}

// Obtener datos del plan 
if (!$id_susc && $new_price_id) {
    $stmt = $conexion->prepare("SELECT id_susc, nombre_susc, precio_susc FROM suscripcion WHERE stripe_price_id = ?");
    $stmt->bind_param("s", $new_price_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $plan = $result->fetch_assoc();
    $stmt->close();

    if ($plan) {
        $id_susc = $plan['id_susc'];
        $nombre_susc = $plan['nombre_susc'];
        $precio_susc = $plan['precio_susc'];
    }
} elseif ($id_susc) {
    $stmt = $conexion->prepare("SELECT nombre_susc, precio_susc FROM suscripcion WHERE id_susc = ?");
    $stmt->bind_param("i", $id_susc);
    $stmt->execute();
    $result = $stmt->get_result();
    $plan = $result->fetch_assoc();
    $stmt->close();

    if ($plan) {
        $nombre_susc = $plan['nombre_susc'];
        $precio_susc = $plan['precio_susc'];
    }
}

// Manejo de cancelaciones
if ($event->type === 'customer.subscription.deleted' || $status === 'canceled') {
    if ($stripe_subscription_id) {
        $observaciones = "Suscripción cancelada por el usuario o Stripe";
        $estado = "cancelado";

        $stmt = $conexion->prepare("
            UPDATE historial
            SET estado = ?, observaciones = ?
            WHERE stripe_subscription_id = ? AND estado = 'activo'
        ");
        $stmt->bind_param("sss", $estado, $observaciones, $stripe_subscription_id);
        $stmt->execute();
        $stmt->close();
    }

// Manejo de nuevas suscripciones o actualizaciones
} elseif ($event->type === 'customer.subscription.created') {
    // Nueva suscripción
    if ($id_emp && $id_susc) {
        $stmt = $conexion->prepare("
            UPDATE historial 
            SET estado = 'inactivo' 
            WHERE Empresa_id_emp = ? AND estado = 'activo'
        ");
        $stmt->bind_param("s", $id_emp);
        $stmt->execute();
        $stmt->close();

        $observaciones = "Nueva suscripción creada";
        $estado = "activo";

        $stmt = $conexion->prepare("
            INSERT INTO historial
            (Empresa_id_emp, Suscripcion_id_susc, nombre_susc, stripe_subscription_id, stripe_customer_id, estado, precio_susc, fecha_contratacion, observaciones)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "sissssdss",
            $id_emp,
            $id_susc,
            $nombre_susc,
            $stripe_subscription_id,
            $customer_id,
            $estado,
            $precio_susc,
            $fecha_actualizacion,
            $observaciones
        );
        $stmt->execute();
        $stmt->close();
    }

} elseif ($event->type === 'customer.subscription.updated') {
    // Actualización: solo modificar el registro actual
    if ($stripe_subscription_id) {
        if ($subscription->cancel_at_period_end) {
            $estado = "pendiente_cancelacion";
            $observaciones = "Cancelación programada al final del período";
        } else {
            $estado = "activo";
            $observaciones = "Actualización de suscripción";
        }

        $stmt = $conexion->prepare("
            UPDATE historial
            SET estado = ?, Suscripcion_id_susc = ?, nombre_susc = ?, precio_susc = ?, observaciones = ?
            WHERE stripe_subscription_id = ?
        ");
        $stmt->bind_param("sisdss", $estado, $id_susc, $nombre_susc, $precio_susc, $observaciones, $stripe_subscription_id);
        $stmt->execute();
        $stmt->close();
    }
}


http_response_code(200);
