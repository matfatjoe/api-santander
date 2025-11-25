<?php

/**
 * Exemplo de gerenciamento de Workspaces
 * 
 * IMPORTANTE: Este arquivo está no .gitignore e não será commitado.
 * Configure suas credenciais reais aqui para testar.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Matfatjoe\SantanderBoleto\Auth\Authenticator;
use Matfatjoe\SantanderBoleto\Auth\TokenRequest;
use Matfatjoe\SantanderBoleto\Workspace\WorkspaceService;
use Matfatjoe\SantanderBoleto\Workspace\CreateWorkspaceRequest;
use Matfatjoe\SantanderBoleto\Workspace\UpdateWorkspaceRequest;
use Matfatjoe\SantanderBoleto\Models\Covenant;
use GuzzleHttp\Client;

// ========================================
// CONFIGURAÇÃO
// ========================================

$pfxPath = __DIR__ . '/../certificate.pfx';
$passphrase = 'SUA_SENHA_AQUI';
$clientId = 'SEU_CLIENT_ID_AQUI';
$clientSecret = 'SEU_CLIENT_SECRET_AQUI';
$baseUrl = 'https://trust-sandbox.api.santander.com.br';

// ========================================
// AUTENTICAÇÃO
// ========================================

try {
    echo "========================================\n";
    echo "Exemplo de Gerenciamento de Workspaces\n";
    echo "========================================\n\n";

    $httpClient = new Client(['timeout' => 30]);
    $authenticator = new Authenticator($httpClient, $baseUrl);
    $tokenRequest = new TokenRequest($pfxPath, $passphrase, $clientId, $clientSecret);

    echo "1. Obtendo token de autenticação...\n";
    $token = $authenticator->getToken($tokenRequest);
    echo "   ✓ Token obtido com sucesso!\n\n";

    // Criar serviço de workspace
    $workspaceService = new WorkspaceService($httpClient, $token, $clientId, $baseUrl);

    // ========================================
    // CRIAR WORKSPACE
    // ========================================

    echo "2. Criando novo workspace...\n";
    $createRequest = new CreateWorkspaceRequest(
        'BILLING',
        [new Covenant(3567206)],  // Substitua pelo código do seu convênio
        'Workspace de teste - ' . date('Y-m-d H:i:s'),
        true,  // bankSlipBillingWebhookActive
        true,  // pixBillingWebhookActive
        'https://seu-webhook.com/santander'  // webhookURL
    );

    $workspace = $workspaceService->create($createRequest);
    echo "   ✓ Workspace criado!\n";
    echo "   ID: " . $workspace->getId() . "\n";
    echo "   Tipo: " . $workspace->getType() . "\n";
    echo "   Descrição: " . $workspace->getDescription() . "\n\n";

    $workspaceId = $workspace->getId();

    // ========================================
    // LISTAR WORKSPACES
    // ========================================

    echo "3. Listando todos os workspaces...\n";
    $workspaces = $workspaceService->list();
    echo "   ✓ Total de workspaces: " . count($workspaces) . "\n";

    foreach ($workspaces as $ws) {
        echo "   - " . $ws->getId() . " (" . $ws->getDescription() . ")\n";
    }
    echo "\n";

    // ========================================
    // CONSULTAR WORKSPACE ESPECÍFICO
    // ========================================

    echo "4. Consultando workspace específico...\n";
    $retrievedWorkspace = $workspaceService->get($workspaceId);
    echo "   ✓ Workspace encontrado!\n";
    echo "   ID: " . $retrievedWorkspace->getId() . "\n";
    echo "   Convênios: " . count($retrievedWorkspace->getCovenants()) . "\n";

    foreach ($retrievedWorkspace->getCovenants() as $covenant) {
        echo "     - Código: " . $covenant->getCode() . "\n";
    }
    echo "\n";

    // ========================================
    // ATUALIZAR WORKSPACE
    // ========================================

    echo "5. Atualizando workspace...\n";
    $updateRequest = new UpdateWorkspaceRequest(
        [new Covenant(3567206)],
        'Workspace atualizado - ' . date('Y-m-d H:i:s')
    );

    $updatedWorkspace = $workspaceService->update($workspaceId, $updateRequest);
    echo "   ✓ Workspace atualizado!\n";
    echo "   Nova descrição: " . $updatedWorkspace->getDescription() . "\n\n";

    // ========================================
    // DELETAR WORKSPACE (COMENTADO POR SEGURANÇA)
    // ========================================

    echo "6. Deletar workspace (descomente para testar)...\n";
    // ATENÇÃO: Descomente a linha abaixo para realmente deletar o workspace
    // $workspaceService->delete($workspaceId);
    // echo "   ✓ Workspace deletado!\n\n";
    echo "   ⚠ Deletar está comentado por segurança\n\n";

    echo "========================================\n";
    echo "✅ Exemplo concluído com sucesso!\n";
    echo "========================================\n";
} catch (\InvalidArgumentException $e) {
    echo "\n❌ ERRO DE CONFIGURAÇÃO\n";
    echo "========================================\n";
    echo $e->getMessage() . "\n\n";
    exit(1);
} catch (\GuzzleHttp\Exception\RequestException $e) {
    echo "\n❌ ERRO DE REQUISIÇÃO HTTP\n";
    echo "========================================\n";
    echo "Mensagem: " . $e->getMessage() . "\n";

    if ($e->hasResponse()) {
        $response = $e->getResponse();
        echo "Status Code: " . $response->getStatusCode() . "\n";
        echo "Resposta: " . $response->getBody()->getContents() . "\n";
    }
    echo "\n";
    exit(1);
} catch (\Exception $e) {
    echo "\n❌ ERRO GERAL\n";
    echo "========================================\n";
    echo "Tipo: " . get_class($e) . "\n";
    echo "Mensagem: " . $e->getMessage() . "\n";
    echo "Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
    exit(1);
}
