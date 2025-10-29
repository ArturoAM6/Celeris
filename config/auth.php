<?php
require_once '../vendor/autoload.php';

$client = new Google\Client();
$client->setAuthConfig('../client_secret_261213902739-spivednak6pa1a6ndkkomb3aqdifokj1.apps.googleusercontent.com.json');
$client->setRedirectUri('http://localhost/equipo-4/Celeris/google_callback.php'); // Cambiar en produccion
$client->setScopes(Google\Service\Drive::DRIVE);
$client->setAccessType('offline');
$client->setPrompt('select_account consent');

$tokenPath = '../token.json';

if (file_exists($tokenPath)) {
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);
    echo var_dump($client);
    die();
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
            echo "Token renovado";
        } else {
            header('Location: ' . $client->createAuthUrl());
        }
    } else {
        echo "Ya autenticado";
    }
} else {
    header('Location: ' . $client->createAuthUrl());
    exit();
}