<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Matfatjoe\SantanderBoleto\Auth\Authenticator;
use Matfatjoe\SantanderBoleto\Auth\TokenRequest;
use Matfatjoe\SantanderBoleto\Models\Token;
use GuzzleHttp\Client;

/**
 * Exemplo de autenticação com certificado .pfx
 */

try {
    // Configuração
    $pfxPath = '/path/to/your/certificate.pfx';  // Caminho para seu certificado .pfx
    $passphrase = 'your_certificate_passphrase';  // Passphrase do certificado
    $clientId = 'your_client_id';                 // Seu Client ID
    $clientSecret = 'your_client_secret';                 // Seu Client Secret

    // Criar cliente HTTP
    $httpClient = new Client([
        'timeout' => 30,
        'connect_timeout' => 10
    ]);

    // Criar autenticador
    $authenticator = new Authenticator($httpClient);

    // Criar requisição de token
    $tokenRequest = new TokenRequest($pfxPath, $passphrase, $clientId, $clientSecret);

    // Obter token
    echo "Obtendo token de autenticação...\n";
    $tokenResponse = $authenticator->getToken($tokenRequest);

    echo "✓ Token obtido com sucesso!\n\n";
    echo "Access Token: " . substr($tokenResponse->getAccessToken(), 0, 20) . "...\n";
    echo "Expira em: " . $tokenResponse->getExpiresIn() . " segundos\n";
    echo "Tipo: " . $tokenResponse->getTokenType() . "\n";
    echo "Session State: " . $tokenResponse->getSessionState() . "\n";
} catch (\InvalidArgumentException $e) {
    echo "✗ Erro de configuração: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    echo "✗ Erro ao obter token: " . $e->getMessage() . "\n";
}
