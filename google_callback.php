<?php
require_once 'vendor/autoload.php';

$client = new Google\Client();
$client->setAuthConfig('client_secret_261213902739-spivednak6pa1a6ndkkomb3aqdifokj1.apps.googleusercontent.com.json');
// $client->setRedirectUri('http://localhost:8000/google_callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    file_put_contents('token.json', json_encode($token));
    header('Location: /public/');
    exit;
}