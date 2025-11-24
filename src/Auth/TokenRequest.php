<?php

namespace Matfatjoe\SantanderBoleto\Auth;

class TokenRequest
{
    private $pfxPath;
    private $passphrase;
    private $clientId;

    /**
     * @param string $pfxPath Path to the .pfx certificate file
     * @param string $passphrase Passphrase for the certificate
     * @param string $clientId Client ID for the API
     */
    public function __construct(string $pfxPath, string $passphrase, string $clientId, string $clientSecret)
    {
        if (!file_exists($pfxPath)) {
            throw new \InvalidArgumentException("Certificate file not found: {$pfxPath}");
        }

        $this->pfxPath = $pfxPath;
        $this->passphrase = $passphrase;
        $this->clientId = $clientId;
    }

    public function getPfxPath(): string
    {
        return $this->pfxPath;
    }

    public function getPassphrase(): string
    {
        return $this->passphrase;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function toArray(): array
    {
        return [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];
    }
}
