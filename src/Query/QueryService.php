<?php

namespace Matfatjoe\SantanderBoleto\Query;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Matfatjoe\SantanderBoleto\Models\Boleto;
use Matfatjoe\SantanderBoleto\Models\Token;

/**
 * Serviço para consulta de Boletos
 */
class QueryService
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
     * Consulta SONDA - Confirma registro do boleto (até D+2)
     *
     * @param string $workspaceId
     * @param string $nsuCode
     * @param string $nsuDate
     * @param string $environment
     * @param string $covenantCode
     * @param string $bankNumber
     * @return Boleto
     * @throws GuzzleException
     * @throws \Exception
     */
    public function querySonda(
        string $workspaceId,
        string $nsuCode,
        string $nsuDate,
        string $environment,
        string $covenantCode,
        string $bankNumber
    ): Boleto {
        // Formato: {nsuCode}.{nsuDate}.{environment}.{covenantCode}.{bankNumber}
        $bankSlipId = implode('.', [$nsuCode, $nsuDate, $environment, $covenantCode, $bankNumber]);

        $response = $this->client->request(
            'GET',
            $this->baseUrl . '/collection_bill_management/v2/workspaces/' . $workspaceId . '/bank_slips/' . $bankSlipId,
            [
                'headers' => [
                    'Authorization' => $this->token->getAuthorizationHeader(),
                    'X-Application-Key' => $this->clientId
                ]
            ]
        );

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        if (!isset($data['nsuCode'])) {
            throw new \Exception('Boleto not found. Status: ' . $response->getStatusCode() . '. Response: ' . $body);
        }

        return Boleto::fromArray($data);
    }

    /**
     * Consulta lista de boletos com filtros
     *
     * @param string $workspaceId
     * @param QueryFilter $filter
     * @return array
     * @throws GuzzleException
     * @throws \Exception
     */
    public function queryList(string $workspaceId, QueryFilter $filter): array
    {
        $queryParams = $filter->toQueryParams();

        $response = $this->client->request(
            'GET',
            $this->baseUrl . '/collection_bill_management/v2/workspaces/' . $workspaceId . '/bank_slips',
            [
                'headers' => [
                    'Authorization' => $this->token->getAuthorizationHeader(),
                    'X-Application-Key' => $this->clientId
                ],
                'query' => $queryParams
            ]
        );

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        $boletos = [];
        if (isset($data['content']) && is_array($data['content'])) {
            foreach ($data['content'] as $boletoData) {
                $boletos[] = Boleto::fromArray($boletoData);
            }
        } elseif (is_array($data)) {
            foreach ($data as $boletoData) {
                if (isset($boletoData['nsuCode'])) {
                    $boletos[] = Boleto::fromArray($boletoData);
                }
            }
        }

        return $boletos;
    }

    /**
     * Consulta detalhada por Nosso Número
     *
     * @param string $beneficiaryCode
     * @param string $bankNumber
     * @param string $queryType (default, duplicate, bankslip, settlement, registry)
     * @return Boleto
     * @throws GuzzleException
     * @throws \Exception
     */
    public function queryByBankNumber(
        string $beneficiaryCode,
        string $bankNumber,
        string $queryType = 'default'
    ): Boleto {
        // Formato: {beneficiaryCode}.{bankNumber}
        $billId = $beneficiaryCode . '.' . $bankNumber;

        $response = $this->client->request(
            'GET',
            $this->baseUrl . '/collection_bill_management/v2/bills/' . $billId,
            [
                'headers' => [
                    'Authorization' => $this->token->getAuthorizationHeader(),
                    'X-Application-Key' => $this->clientId
                ],
                'query' => ['tipoConsulta' => $queryType]
            ]
        );

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        if (!isset($data['nsuCode']) && !isset($data['bankNumber'])) {
            throw new \Exception('Boleto not found. Status: ' . $response->getStatusCode() . '. Response: ' . $body);
        }

        return Boleto::fromArray($data);
    }

    /**
     * Consulta detalhada por Seu Número
     *
     * @param string $beneficiaryCode
     * @param string $clientNumber
     * @param string $dueDate
     * @param string $nominalValue
     * @return Boleto
     * @throws GuzzleException
     * @throws \Exception
     */
    public function queryByClientNumber(
        string $beneficiaryCode,
        string $clientNumber,
        string $dueDate,
        string $nominalValue
    ): Boleto {
        $response = $this->client->request(
            'GET',
            $this->baseUrl . '/collection_bill_management/v2/bills',
            [
                'headers' => [
                    'Authorization' => $this->token->getAuthorizationHeader(),
                    'X-Application-Key' => $this->clientId
                ],
                'query' => [
                    'beneficiaryCode' => $beneficiaryCode,
                    'clientNumber' => $clientNumber,
                    'dueDate' => $dueDate,
                    'nominalValue' => $nominalValue
                ]
            ]
        );

        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        if (!isset($data['nsuCode']) && !isset($data['bankNumber'])) {
            throw new \Exception('Boleto not found. Status: ' . $response->getStatusCode() . '. Response: ' . $body);
        }

        return Boleto::fromArray($data);
    }
}
