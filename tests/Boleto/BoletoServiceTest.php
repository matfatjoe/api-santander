<?php

namespace Matfatjoe\SantanderBoleto\Tests\Boleto;

use Matfatjoe\SantanderBoleto\Boleto\BoletoService;
use Matfatjoe\SantanderBoleto\Boleto\RegisterBoletoRequest;
use Matfatjoe\SantanderBoleto\Boleto\InstructionRequest;
use Matfatjoe\SantanderBoleto\Models\Boleto;
use Matfatjoe\SantanderBoleto\Models\Payer;
use Matfatjoe\SantanderBoleto\Models\Beneficiary;
use Matfatjoe\SantanderBoleto\Models\Token;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class BoletoServiceTest extends TestCase
{
    private function createMockToken(): Token
    {
        return new Token('test_token', 900, 'bearer');
    }

    private function createMockBoleto(): Boleto
    {
        return new Boleto(
            'TESTE',
            '1014',
            '2023-05-09',
            '3567206',
            '1014',
            '123',
            '2023-05-09',
            '2023-05-09',
            'teste liq abat',
            '1.0',
            new Payer('João', 'CPF', '12345678901', 'Rua 1', 'Bairro', 'Cidade', 'SP', '12345678'),
            new Beneficiary('Empresa', 'CNPJ', '12345678000199')
        );
    }

    public function testRegisterBoleto()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
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
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new BoletoService($client, $this->createMockToken(), 'test_client_id');

        $request = new RegisterBoletoRequest($this->createMockBoleto());
        $boleto = $service->register('test-workspace-id', $request);

        $this->assertInstanceOf(Boleto::class, $boleto);
        $this->assertEquals('1014', $boleto->getNsuCode());
        $this->assertEquals('3567206', $boleto->getCovenantCode());
    }

    public function testSendInstruction()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'covenantCode' => '3567206',
                'bankNumber' => '1014',
                'message' => 'Alteração realizada com sucesso'
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new BoletoService($client, $this->createMockToken(), 'test_client_id');

        $request = new InstructionRequest('3567206', '1014', [
            'discount' => [
                'type' => 'VALOR_DIA_CORRIDO',
                'discountOne' => ['value' => 0.60]
            ]
        ]);

        $response = $service->sendInstruction($request);

        $this->assertEquals('Alteração realizada com sucesso', $response['message']);
    }

    public function testRegisterBoletoFailure()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to register boleto');

        $mock = new MockHandler([
            new Response(400, [], json_encode(['error' => 'Invalid data']))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new BoletoService($client, $this->createMockToken(), 'test_client_id');

        $request = new RegisterBoletoRequest($this->createMockBoleto());
        $service->register('test-workspace-id', $request);
    }
}
