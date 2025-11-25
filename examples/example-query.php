<?php

/**
 * Exemplo de uso do módulo de Query (Consulta de Boletos)
 * 
 * Este exemplo demonstra como:
 * 1. Autenticar na API
 * 2. Consultar boletos usando diferentes métodos
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Matfatjoe\SantanderBoleto\Auth\Authenticator;
use Matfatjoe\SantanderBoleto\Auth\TokenRequest;
use Matfatjoe\SantanderBoleto\HttpClientFactory;
use Matfatjoe\SantanderBoleto\Query\QueryService;
use Matfatjoe\SantanderBoleto\Query\QueryFilter;

// ========================================
// CONFIGURAÇÃO
// ========================================

$pfxPath = __DIR__ . '/../certificate.pfx';
$passphrase = 'sua_senha_do_certificado';
$clientId = 'seu_client_id';
$clientSecret = 'seu_client_secret';
$covenantCode = 'seu_codigo_convenio';
$workspaceId = 'seu_workspace_id';
$baseUrl = 'https://trust-sandbox.api.santander.com.br'; // Sandbox

// ========================================
// EXEMPLO DE USO
// ========================================

try {
    // 1. Autenticação
    echo "Autenticando...\n";
    $tokenRequest = new TokenRequest($pfxPath, $passphrase, $clientId, $clientSecret);
    $httpClient = HttpClientFactory::createFromTokenRequest($tokenRequest);
    $authenticator = new Authenticator($httpClient, $baseUrl);
    $token = $authenticator->getToken($tokenRequest);
    echo "✅ Autenticado com sucesso!\n\n";

    $queryService = new QueryService($httpClient, $token, $clientId, $baseUrl);

    // ========================================
    // 2. CONSULTA SONDA (Confirma registro do boleto - até D+2)
    // ========================================
    echo "2️⃣  CONSULTA SONDA\n";
    echo "-----------------------------------\n";

    try {
        $boleto = $queryService->querySonda(
            $workspaceId,
            '1014',              // NSU Code
            '2023-05-09',        // NSU Date
            'TESTE',             // Environment (TESTE ou PRODUCAO)
            $covenantCode,       // Covenant Code
            '1014'               // Bank Number (Nosso Número)
        );

        echo "✅ Boleto encontrado!\n";
        echo "   NSU Code: " . $boleto->getNsuCode() . "\n";
        echo "   Nosso Número: " . $boleto->getBankNumber() . "\n";
        echo "   Valor: " . $boleto->getNominalValue() . "\n\n";
    } catch (\Exception $e) {
        echo "❌ Boleto não encontrado: " . $e->getMessage() . "\n\n";
    }

    // ========================================
    // 3. CONSULTA LISTA (Boletos liquidados)
    // ========================================
    echo "3️⃣  CONSULTA LISTA DE BOLETOS LIQUIDADOS\n";
    echo "-----------------------------------\n";

    $filter = new QueryFilter(
        'LIQUIDADO',                    // Status
        10,                             // Limit (máx 1000)
        null,                           // Bank Number (opcional)
        null,                           // Client Number (opcional)
        null,                           // Due Date Initial (opcional)
        null,                           // Due Date Final (opcional)
        '2023-05-01',                   // Payment Date Initial (obrigatório para LIQUIDADO)
        '2023-05-31'                    // Payment Date Final (obrigatório para LIQUIDADO)
    );

    $boletos = $queryService->queryList($workspaceId, $filter);

    echo "✅ Encontrados " . count($boletos) . " boleto(s)\n";
    foreach ($boletos as $boleto) {
        echo "   - Nosso Número: " . $boleto->getBankNumber() .
            " | Valor: " . $boleto->getNominalValue() . "\n";
    }
    echo "\n";

    // ========================================
    // 4. CONSULTA DETALHADA POR NOSSO NÚMERO
    // ========================================
    echo "4️⃣  CONSULTA DETALHADA POR NOSSO NÚMERO\n";
    echo "-----------------------------------\n";

    try {
        $boleto = $queryService->queryByBankNumber(
            $covenantCode,      // Beneficiary Code (Código do Convênio)
            '1014',             // Bank Number (Nosso Número)
            'default'           // Query Type: default, duplicate, bankslip, settlement, registry
        );

        echo "✅ Boleto encontrado!\n";
        echo "   Nosso Número: " . $boleto->getBankNumber() . "\n";
        echo "   Pagador: " . $boleto->getPayer()->getName() . "\n";
        echo "   Valor: " . $boleto->getNominalValue() . "\n\n";
    } catch (\Exception $e) {
        echo "❌ Boleto não encontrado: " . $e->getMessage() . "\n\n";
    }

    // ========================================
    // 5. CONSULTA DETALHADA POR SEU NÚMERO
    // ========================================
    echo "5️⃣  CONSULTA DETALHADA POR SEU NÚMERO\n";
    echo "-----------------------------------\n";

    try {
        $boleto = $queryService->queryByClientNumber(
            $covenantCode,      // Beneficiary Code
            '123',              // Client Number (Seu Número)
            '2023-05-09',       // Due Date
            '1.00'              // Nominal Value
        );

        echo "✅ Boleto encontrado!\n";
        echo "   Seu Número: " . $boleto->getClientNumber() . "\n";
        echo "   Nosso Número: " . $boleto->getBankNumber() . "\n";
        echo "   Vencimento: " . $boleto->getDueDate() . "\n\n";
    } catch (\Exception $e) {
        echo "❌ Boleto não encontrado: " . $e->getMessage() . "\n\n";
    }

    echo "✅ Exemplos de consulta concluídos!\n";
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
