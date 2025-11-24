<?php

namespace Matfatjoe\SantanderBoleto\Auth;

use Matfatjoe\SantanderBoleto\Models\Token;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;

class Authenticator
{
    private $client;
    private $baseUrl;

    public function __construct(
        ClientInterface $client,
        string $baseUrl = 'https://trust-sandbox.api.santander.com.br'
    ) {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
    }

    /**
     * Get OAuth2 token using mTLS certificate authentication
     *
     * @param TokenRequest $request
     * @return Token
     * @throws GuzzleException
     * @throws \Exception
     */
    public function getToken(TokenRequest $request): Token
    {
        // Extract PEM certificate and key from PFX file
        $pfxContent = file_get_contents($request->getPfxPath());
        $certs = [];

        if (!openssl_pkcs12_read($pfxContent, $certs, $request->getPassphrase())) {
            throw new \Exception('Failed to read PFX certificate. Check passphrase.');
        }

        // Create temporary files for certificate and private key
        $certFile = tempnam(sys_get_temp_dir(), 'cert');
        $keyFile = tempnam(sys_get_temp_dir(), 'key');

        file_put_contents($certFile, $certs['cert']);
        file_put_contents($keyFile, $certs['pkey']);

        try {
            $response = $this->client->request('POST', $this->baseUrl . '/auth/oauth/v2/token', [
                'form_params' => $request->toArray(),
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'cert' => [$certFile, $request->getPassphrase()],
                'ssl_key' => $keyFile,
                'verify' => true
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (!isset($data['access_token'])) {
                throw new \Exception('Failed to retrieve access token');
            }

            return Token::fromArray($data);
        } finally {
            // Clean up temporary files
            if (file_exists($certFile)) {
                unlink($certFile);
            }
            if (file_exists($keyFile)) {
                unlink($keyFile);
            }
        }
    }
}
