<?php
$captcha = $_POST['g-recaptcha-response'];
$secret = '';
$response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$captcha");
var_dump(json_decode($response, true));
?>
