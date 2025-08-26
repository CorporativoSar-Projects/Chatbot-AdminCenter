<?php
require 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('...');//private key de stripe

if (isset($_GET['session_id'])) {
    $session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);
    $email = $session->customer_email;

    echo "<h1>¡Gracias por tu pago, $email!</h1>";

} elseif ($_GET['plan'] === 'free') {
    echo "<h1>Registro exitoso con plan gratuito</h1>";

}
