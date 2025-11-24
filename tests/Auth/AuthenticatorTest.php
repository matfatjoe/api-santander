<?php

namespace Matfatjoe\SantanderBoleto\Tests\Auth;

use Matfatjoe\SantanderBoleto\Auth\Authenticator;
use Matfatjoe\SantanderBoleto\Auth\TokenRequest;
use Matfatjoe\SantanderBoleto\Models\Token;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class AuthenticatorTest extends TestCase
{
    private $testCertPath;

    protected function setUp(): void
    {
        // Create a mock certificate file for testing
        $this->testCertPath = sys_get_temp_dir() . '/test_cert.pfx';

        // Create a self-signed certificate for testing
        $this->createTestCertificate();
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testCertPath)) {
            unlink($this->testCertPath);
        }
    }

    private function createTestCertificate(): void
    {
        // Generate a private key
        $privateKey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        // Generate a self-signed certificate
        $dn = [
            "countryName" => "BR",
            "stateOrProvinceName" => "SP",
            "localityName" => "Sao Paulo",
            "organizationName" => "Test",
            "commonName" => "test.local"
        ];

        $csr = openssl_csr_new($dn, $privateKey);
        $cert = openssl_csr_sign($csr, null, $privateKey, 365);

        // Export to PFX
        $certs = [];
        openssl_x509_export($cert, $certs['cert']);
        openssl_pkey_export($privateKey, $certs['pkey']);

        openssl_pkcs12_export_to_file($cert, $this->testCertPath, $privateKey, 'test123');
    }

    public function testGetTokenSuccess()
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'access_token' => 'test_token_12345',
                'expires_in' => 900,
                'token_type' => 'bearer',
                'not-before-policy' => 1614173461,
                'session_state' => 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaaa',
                'scope' => ''
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $authenticator = new Authenticator($client);
        $request = new TokenRequest($this->testCertPath, 'test123', 'test_client_id', 'test_client_secret');

        $response = $authenticator->getToken($request);

        $this->assertInstanceOf(Token::class, $response);
        $this->assertEquals('test_token_12345', $response->getAccessToken());
        $this->assertEquals(900, $response->getExpiresIn());
        $this->assertEquals('bearer', $response->getTokenType());
        $this->assertEquals(1614173461, $response->getNotBeforePolicy());
        $this->assertEquals('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaaa', $response->getSessionState());
        $this->assertEquals('', $response->getScope());
    }

    public function testGetTokenFailure()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to retrieve access token');

        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'error' => 'invalid_grant'
            ]))
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $authenticator = new Authenticator($client);
        $request = new TokenRequest($this->testCertPath, 'test123', 'test_client_id', 'test_client_secret');

        $authenticator->getToken($request);
    }

    public function testInvalidCertificatePath()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Certificate file not found');

        new TokenRequest('/invalid/path/cert.pfx', 'password', 'client_id', 'client_secret');
    }
}
