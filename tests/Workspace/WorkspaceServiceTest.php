<?php

namespace Matfatjoe\SantanderBoleto\Tests\Workspace;

use Matfatjoe\SantanderBoleto\Models\Covenant;
use Matfatjoe\SantanderBoleto\Models\Token;
use Matfatjoe\SantanderBoleto\Models\Workspace;
use Matfatjoe\SantanderBoleto\Workspace\CreateWorkspaceRequest;
use Matfatjoe\SantanderBoleto\Workspace\UpdateWorkspaceRequest;
use Matfatjoe\SantanderBoleto\Workspace\WorkspaceService;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class WorkspaceServiceTest extends TestCase
{
    private function createMockToken(): Token
    {
        return new Token('test_token', 900, 'bearer');
    }

    public function testCreateWorkspace()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'id' => '7873ca2b-8d45-41f8-8fb0-c8aa834cd2ca',
                'type' => 'BILLING',
                'covenants' => [
                    ['code' => 3567206]
                ],
                'description' => 'Testando',
                'bankSlipBillingWebhookActive' => true,
                'pixBillingWebhookActive' => true,
                'webhookURL' => 'https://teste',
                'createdAt' => '2023-05-09T10:00:00Z',
                'updatedAt' => '2023-05-09T10:00:00Z'
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new WorkspaceService($client, $this->createMockToken(), 'test_client_id');

        $request = new CreateWorkspaceRequest(
            'BILLING',
            [new Covenant(3567206)],
            'Testando',
            true,
            true,
            'https://teste'
        );

        $workspace = $service->create($request);

        $this->assertInstanceOf(Workspace::class, $workspace);
        $this->assertEquals('7873ca2b-8d45-41f8-8fb0-c8aa834cd2ca', $workspace->getId());
        $this->assertEquals('BILLING', $workspace->getType());
        $this->assertEquals('Testando', $workspace->getDescription());
        $this->assertTrue($workspace->isBankSlipBillingWebhookActive());
        $this->assertTrue($workspace->isPixBillingWebhookActive());
        $this->assertEquals('https://teste', $workspace->getWebhookURL());
        $this->assertCount(1, $workspace->getCovenants());
        $this->assertEquals(3567206, $workspace->getCovenants()[0]->getCode());
    }

    public function testListWorkspaces()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'content' => [
                    [
                        'id' => 'workspace-1',
                        'type' => 'BILLING',
                        'status' => 'ACTIVE',
                        'covenants' => [['code' => 123]]
                    ],
                    [
                        'id' => 'workspace-2',
                        'type' => 'BILLING',
                        'status' => 'ACTIVE',
                        'covenants' => [['code' => 456]]
                    ]
                ]
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new WorkspaceService($client, $this->createMockToken(), 'test_client_id');
        $workspaces = $service->list();

        $this->assertCount(2, $workspaces);
        $this->assertEquals('workspace-1', $workspaces[0]->getId());
        $this->assertEquals('workspace-2', $workspaces[1]->getId());
    }

    public function testGetWorkspace()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'id' => '7873ca2b-8d45-41f8-8fb0-c8aa834cd2ca',
                'type' => 'BILLING',
                'covenants' => [['code' => 3567206]],
                'description' => 'Workspace de teste'
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new WorkspaceService($client, $this->createMockToken(), 'test_client_id');
        $workspace = $service->get('7873ca2b-8d45-41f8-8fb0-c8aa834cd2ca');

        $this->assertInstanceOf(Workspace::class, $workspace);
        $this->assertEquals('7873ca2b-8d45-41f8-8fb0-c8aa834cd2ca', $workspace->getId());
        $this->assertEquals('Workspace de teste', $workspace->getDescription());
    }

    public function testUpdateWorkspace()
    {
        $mock = new MockHandler([
            // Resposta do PATCH (parcial)
            new Response(200, [], json_encode([
                'description' => 'Descrição atualizada',
                'covenants' => [['code' => 9999]]
            ])),
            // Resposta do GET (completa)
            new Response(200, [], json_encode([
                'id' => '7873ca2b-8d45-41f8-8fb0-c8aa834cd2ca',
                'type' => 'BILLING',
                'covenants' => [['code' => 9999]],
                'description' => 'Descrição atualizada'
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new WorkspaceService($client, $this->createMockToken(), 'test_client_id');

        $request = new UpdateWorkspaceRequest(
            [new Covenant(9999)],
            'Descrição atualizada'
        );

        $workspace = $service->update('7873ca2b-8d45-41f8-8fb0-c8aa834cd2ca', $request);

        $this->assertEquals('Descrição atualizada', $workspace->getDescription());
        $this->assertEquals(9999, $workspace->getCovenants()[0]->getCode());
    }

    public function testDeleteWorkspace()
    {
        $mock = new MockHandler([
            new Response(204, [])
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new WorkspaceService($client, $this->createMockToken(), 'test_client_id');

        // Não deve lançar exceção
        $service->delete('7873ca2b-8d45-41f8-8fb0-c8aa834cd2ca');

        $this->assertTrue(true); // Se chegou aqui, passou
    }

    public function testCreateWorkspaceFailure()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to create workspace');

        $mock = new MockHandler([
            new Response(200, [], json_encode(['error' => 'Invalid data']))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $service = new WorkspaceService($client, $this->createMockToken(), 'test_client_id');

        $request = new CreateWorkspaceRequest(
            'BILLING',
            [new Covenant(3567206)]
        );

        $service->create($request);
    }
}
