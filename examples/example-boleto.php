<?php

/**
 * Exemplo de uso do módulo de Boleto
 * 
 * Este exemplo demonstra como:
 * 1. Autenticar na API
 * 2. Criar um workspace
 * 3. Registrar um boleto no workspace
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Matfatjoe\SantanderBoleto\Auth\Authenticator;
use Matfatjoe\SantanderBoleto\Auth\TokenRequest;
use Matfatjoe\SantanderBoleto\HttpClientFactory;
use Matfatjoe\SantanderBoleto\Workspace\WorkspaceService;
use Matfatjoe\SantanderBoleto\Workspace\CreateWorkspaceRequest;
use Matfatjoe\SantanderBoleto\Boleto\BoletoService;
use Matfatjoe\SantanderBoleto\Boleto\RegisterBoletoRequest;
use Matfatjoe\SantanderBoleto\Models\Covenant;
use Matfatjoe\SantanderBoleto\Models\Boleto;
use Matfatjoe\SantanderBoleto\Models\Payer;
use Matfatjoe\SantanderBoleto\Models\Beneficiary;

// ========================================
// CONFIGURAÇÃO
// ========================================

$pfxPath = __DIR__ . '/../certificate.pfx';
$passphrase = 'sua_senha_do_certificado';
$clientId = 'seu_client_id';
$clientSecret = 'seu_client_secret';
$covenantCode = 'seu_codigo_convenio';
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

    // 2. Criar Workspace
    echo "Criando workspace...\n";
    $workspaceService = new WorkspaceService($httpClient, $token, $clientId, $baseUrl);

    $createWorkspaceRequest = new CreateWorkspaceRequest(
        'BILLING',
        [new Covenant($covenantCode)],
        'Meu Workspace de Boletos',
        true,  // Webhook para boletos ativo
        true,  // Webhook para PIX ativo
        'https://meu-site.com/webhook/santander'
    );

    $workspace = $workspaceService->create($createWorkspaceRequest);
    $workspaceId = $workspace->getId();
    echo "✅ Workspace criado! ID: {$workspaceId}\n\n";

    // 3. Registrar Boleto
    echo "Registrando boleto...\n";
    $boletoService = new BoletoService($httpClient, $token, $clientId, $baseUrl);

    // Dados do Pagador (quem vai pagar o boleto)
    $payer = new Payer(
        'João da Silva',
        'CPF',
        '12345678901',
        'Rua das Flores, 123',
        'Centro',
        'São Paulo',
        'SP',
        '01234-567'
    );

    // Dados do Beneficiário (quem vai receber o pagamento)
    $beneficiary = new Beneficiary(
        'Minha Empresa LTDA',
        'CNPJ',
        '12345678000199'
    );

    // Criar o Boleto
    $boleto = new Boleto(
        'TESTE',                    // Ambiente
        '1234',                     // NSU Code (seu controle interno)
        date('Y-m-d'),              // NSU Date
        $covenantCode,              // Código do Convênio
        '000001',                   // Nosso Número (sequencial único)
        'CLI-001',                  // Número do Cliente
        date('Y-m-d', strtotime('+7 days')), // Vencimento (7 dias)
        date('Y-m-d'),              // Data de Emissão
        'VENDA-001',                // Código do Participante
        '150.00',                   // Valor Nominal
        $payer,
        $beneficiary,
        null,                       // Desconto (opcional)
        null,                       // Multa (opcional)
        null,                       // Juros (opcional)
        '30',                       // Dias para baixa automática
        'REGISTRO',
        'DUPLICATA_MERCANTIL',
        null,                       // Valor de dedução (opcional)
        null,                       // Código de barras (preenchido pela API)
        null,                       // Linha digitável (preenchido pela API)
        null,                       // QR Code PIX (preenchido pela API)
        null,                       // URL QR Code (preenchido pela API)
        [
            'Pagamento referente à compra #001',
            'Não receber após o vencimento'
        ]
    );

    $registerRequest = new RegisterBoletoRequest($boleto);
    $registeredBoleto = $boletoService->register($workspaceId, $registerRequest);

    echo "✅ Boleto registrado com sucesso!\n";
    echo "   NSU Code: " . $registeredBoleto->getNsuCode() . "\n";
    echo "   Nosso Número: " . $registeredBoleto->getBankNumber() . "\n";
    echo "   Código de Barras: " . $registeredBoleto->getBarcode() . "\n";
    echo "   Linha Digitável: " . $registeredBoleto->getDigitableLine() . "\n";

    if ($registeredBoleto->getQrCodePix()) {
        echo "   QR Code PIX: " . $registeredBoleto->getQrCodePix() . "\n";
    }

    echo "\n✅ Exemplo concluído com sucesso!\n";
} catch (\Exception $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
