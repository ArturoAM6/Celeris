<?php
require_once 'vendor/autoload.php';

$client = new Google\Client();
$client->setAuthConfig(__DIR__ . '/client_secret_261213902739-spivednak6pa1a6ndkkomb3aqdifokj1.apps.googleusercontent.com.json');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    file_put_contents(__DIR__ . '/token.json', json_encode($token));
    header('Location: ' . BASE_URL);
    exit;
}