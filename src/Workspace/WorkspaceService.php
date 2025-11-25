<?php

namespace Matfatjoe\SantanderBoleto\Workspace;

use Matfatjoe\SantanderBoleto\Models\Token;
use Matfatjoe\SantanderBoleto\Models\Workspace;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Serviço para gerenciamento de Workspaces
 */
class WorkspaceService
{
    private $client;
    private $baseUrl;
    private $token;
    private $clientId;

    public function __construct(
        ClientInterface $client,
        Token $token,
        string $clientId,
        string $baseUrl = 'https://trust-sandbox.api.santander.com.br'
    ) {
        $this->client = $client;
        $this->token = $token;
        $this->clientId = $clientId;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Cria um novo workspace
     *
     * @param CreateWorkspaceRequest $request
     * @return Workspace
     * @throws GuzzleException
     * @throws \Exception
     */
    public function create(CreateWorkspaceRequest $request): Workspace
    {
        $response = $this->client->request('POST', $this->baseUrl . '/collection_bill_management/v2/workspaces', [
            'headers' => [
                'Authorization' => $this->token->getAuthorizationHeader(),
                'X-Application-Key' => $this->clientId,
                'Content-Type' => 'application/json'
            ],
            'json' => $request->toArray()
        ]);

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        if (!isset($data['id'])) {
            $errorMsg = 'Failed to create workspace. ';
            $errorMsg .= 'Status: ' . $response->getStatusCode() . '. ';
            $errorMsg .= 'Response: ' . $body;
            throw new \Exception($errorMsg);
        }

        return Workspace::fromArray($data);
    }

    /**
     * Lista todos os workspaces
     *
     * @return Workspace[]
     * @throws GuzzleException
     */
    public function list(): array
    {
        $response = $this->client->request('GET', $this->baseUrl . '/collection_bill_management/v2/workspaces', [
            'headers' => [
                'Authorization' => $this->token->getAuthorizationHeader(),
                'X-Application-Key' => $this->clientId
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        $workspaces = [];
        // A API retorna a lista dentro de "content"
        if (isset($data['content']) && is_array($data['content'])) {
            foreach ($data['content'] as $workspaceData) {
                $workspaces[] = Workspace::fromArray($workspaceData);
            }
        } elseif (isset($data['workspaces']) && is_array($data['workspaces'])) {
            // Mantendo compatibilidade caso mude
            foreach ($data['workspaces'] as $workspaceData) {
                $workspaces[] = Workspace::fromArray($workspaceData);
            }
        } elseif (is_array($data)) {
            // Se a resposta for diretamente um array de workspaces
            foreach ($data as $workspaceData) {
                if (isset($workspaceData['id'])) {
                    $workspaces[] = Workspace::fromArray($workspaceData);
                }
            }
        }

        return $workspaces;
    }

    /**
     * Obtém um workspace específico por ID
     *
     * @param string $workspaceId
     * @return Workspace
     * @throws GuzzleException
     * @throws \Exception
     */
    public function get(string $workspaceId): Workspace
    {
        $response = $this->client->request('GET', $this->baseUrl . '/collection_bill_management/v2/workspaces/' . $workspaceId, [
            'headers' => [
                'Authorization' => $this->token->getAuthorizationHeader(),
                'X-Application-Key' => $this->clientId
            ]
        ]);

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        if (!isset($data['id'])) {
            $errorMsg = 'Workspace not found. ';
            $errorMsg .= 'Status: ' . $response->getStatusCode() . '. ';
            $errorMsg .= 'Response: ' . $body;
            throw new \Exception($errorMsg);
        }

        return Workspace::fromArray($data);
    }

    /**
     * Atualiza um workspace existente
     *
     * @param string $workspaceId
     * @param UpdateWorkspaceRequest $request
     * @return Workspace
     * @throws GuzzleException
     * @throws \Exception
     */
    public function update(string $workspaceId, UpdateWorkspaceRequest $request): Workspace
    {
        $response = $this->client->request('PATCH', $this->baseUrl . '/collection_bill_management/v2/workspaces/' . $workspaceId, [
            'headers' => [
                'Authorization' => $this->token->getAuthorizationHeader(),
                'X-Application-Key' => $this->clientId,
                'Content-Type' => 'application/json'
            ],
            'json' => $request->toArray()
        ]);

        // A resposta do update não retorna o objeto completo (falta ID, type, etc)
        // Então buscamos o workspace atualizado para retornar o objeto completo
        if ($response->getStatusCode() === 200) {
            return $this->get($workspaceId);
        }

        $body = $response->getBody()->getContents();
        throw new \Exception('Failed to update workspace. Status: ' . $response->getStatusCode() . '. Response: ' . $body);
    }

    /**
     * Deleta um workspace
     *
     * @param string $workspaceId
     * @return void
     * @throws GuzzleException
     */
    public function delete(string $workspaceId): void
    {
        $this->client->request('DELETE', $this->baseUrl . '/collection_bill_management/v2/workspaces/' . $workspaceId, [
            'headers' => [
                'Authorization' => $this->token->getAuthorizationHeader(),
                'X-Application-Key' => $this->clientId
            ]
        ]);
    }
}
