<?php

namespace Matfatjoe\SantanderBoleto\Tests\Query;

use Matfatjoe\SantanderBoleto\Query\QueryService;
use Matfatjoe\SantanderBoleto\Query\QueryFilter;
use Matfatjoe\SantanderBoleto\Models\Boleto;
use Matfatjoe\SantanderBoleto\Models\Token;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class QueryServiceTest extends TestCase
{
    private function createMockToken(): Token
    {
        return new Token('test_token', 900, 'bearer');
    }

    private function createMockBoletoResponse(): array
    {
        return [
            'nsuCode' => '1014',
            'nsuDate' => '2023-05-09',
            'environment' => 'TESTE',
            'covenantCode' => '3567206',
            'bankNumber' => '1014',
            'clientNumber' => '123',
            'dueDate' => '2023-05-09',
            'issueDate' => '2023-05-09',
            'nominalValue' => '1.0',
            'payer' => [
                'name' => 'João',
                'documentType' => 'CPF',
                'documentNumber' => '12345678901',
                'address' => 'Rua 1',
                'neighborhood' => 'Bairro',
                'city' => 'Cidade',
                'state' => 'SP',
                'zipCode' => '12345678'
            ],
            'beneficiary' => [
                'name' => 'Empresa',
                'documentType' => 'CNPJ',
                'documentNumber' => '12345678000199'
            ],
            'paymentType' => 'REGISTRO',
            'documentKind' => 'DUPLICATA_MERCANTIL'
        ];
    }

    public function testQuerySonda()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->createMockBoletoResponse()))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new QueryService($client, $this->createMockToken(), 'test_client_id');

        $boleto = $service->querySonda(
            'workspace-id',
            '1014',
            '2023-05-09',
            'TESTE',
            '3567206',
            '1014'
        );

        $this->assertInstanceOf(Boleto::class, $boleto);
        $this->assertEquals('1014', $boleto->getNsuCode());
    }

    public function testQueryList()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'content' => [
                    $this->createMockBoletoResponse(),
                    $this->createMockBoletoResponse()
                ]
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new QueryService($client, $this->createMockToken(), 'test_client_id');

        $filter = new QueryFilter(
            'LIQUIDADO',
            10,
            null,
            null,
            null,
            null,
            '2023-05-01',
            '2023-05-31'
        );

        $boletos = $service->queryList('workspace-id', $filter);

        $this->assertCount(2, $boletos);
        $this->assertInstanceOf(Boleto::class, $boletos[0]);
    }

    public function testQueryByBankNumber()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->createMockBoletoResponse()))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new QueryService($client, $this->createMockToken(), 'test_client_id');

        $boleto = $service->queryByBankNumber('3567206', '1014', 'default');

        $this->assertInstanceOf(Boleto::class, $boleto);
        $this->assertEquals('1014', $boleto->getBankNumber());
    }

    public function testQueryByClientNumber()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode($this->createMockBoletoResponse()))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new QueryService($client, $this->createMockToken(), 'test_client_id');

        $boleto = $service->queryByClientNumber(
            '3567206',
            '123',
            '2023-05-09',
            '1.0'
        );

        $this->assertInstanceOf(Boleto::class, $boleto);
        $this->assertEquals('123', $boleto->getClientNumber());
    }

    public function testQueryListEmptyResults()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['content' => []]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new QueryService($client, $this->createMockToken(), 'test_client_id');

        $filter = new QueryFilter('LIQUIDADO');
        $boletos = $service->queryList('workspace-id', $filter);

        $this->assertCount(0, $boletos);
    }
}
