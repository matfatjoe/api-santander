<?php

namespace Matfatjoe\SantanderBoleto\Boleto;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Matfatjoe\SantanderBoleto\Models\Boleto;
use Matfatjoe\SantanderBoleto\Models\Token;

/**
 * Serviço para gerenciamento de Boletos
 */
class BoletoService
{
    private $client;
    private $token;
    private $clientId;
    private $baseUrl;

    public function __construct(Client $client, Token $token, string $clientId, string $baseUrl = 'https://trust-sandbox.api.santander.com.br')
    {
        $this->client = $client;
        $this->token = $token;
        $this->clientId = $clientId;
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Registra um novo boleto
     *
     * @param string $workspaceId
     * @param RegisterBoletoRequest $request
     * @return Boleto
     * @throws GuzzleException
     * @throws \Exception
     */
    public function register(string $workspaceId, RegisterBoletoRequest $request): Boleto
    {
        try {
            $response = $this->client->request('POST', $this->baseUrl . '/collection_bill_management/v2/workspaces/' . $workspaceId . '/bank_slips', [
                'headers' => [
                    'Authorization' => $this->token->getAuthorizationHeader(),
                    'X-Application-Key' => $this->clientId,
                    'Content-Type' => 'application/json'
                ],
                'json' => $request->toArray()
            ]);

            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);

            if (!isset($data['nsuCode'])) {
                throw new \Exception('Failed to register boleto. Status: ' . $response->getStatusCode() . '. Response: ' . $body);
            }

            return Boleto::fromArray($data);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $responseBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : $e->getMessage();
            throw new \Exception('Failed to register boleto. ' . $responseBody);
        }
    }

    /**
     * Envia uma instrução para um boleto (PATCH)
     *
     * @param string $workspaceId
     * @param InstructionRequest $request
     * @return array
     * @throws GuzzleException
     * @throws \Exception
     */
    public function sendInstruction(string $workspaceId, InstructionRequest $request): array
    {
        $response = $this->client->request('PATCH', $this->baseUrl . '/collection_bill_management/v2/workspaces/' . $workspaceId . '/bank_slips', [
            'headers' => [
                'Authorization' => $this->token->getAuthorizationHeader(),
                'X-Application-Key' => $this->clientId,
                'Content-Type' => 'application/json'
            ],
            'json' => $request->toArray()
        ]);

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to send instruction. Status: ' . $response->getStatusCode() . '. Response: ' . $body);
        }

        return $data;
    }
}
