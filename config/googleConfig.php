
<?php
require_once '../vendor/autoload.php';

function getClient() {
    $client = new Google\Client();
    $client->setAuthConfig('../client_secret_261213902739-spivednak6pa1a6ndkkomb3aqdifokj1.apps.googleusercontent.com.json');
    $client->setAccessType('offline');
    
    $tokenPath = '../token.json';
    
    if (!file_exists($tokenPath)) {
        throw new Exception('No autenticado. Visita /auth.php primero');
    }
    
    $accessToken = json_decode(file_get_contents($tokenPath), true);
    $client->setAccessToken($accessToken);
    
    if ($client->isAccessTokenExpired()) {
        if ($client->getRefreshToken()) {
            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            file_put_contents($tokenPath, json_encode($client->getAccessToken()));
        } else {
            throw new Exception('Token expirado. Visita /auth.php de nuevo');
        }
    }
    
    return $client;
}