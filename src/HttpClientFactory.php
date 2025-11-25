<?php

namespace Matfatjoe\SantanderBoleto;

use Matfatjoe\SantanderBoleto\Auth\TokenRequest;
use GuzzleHttp\Client;

/**
 * Factory para criar cliente HTTP configurado com certificado mTLS
 */
class HttpClientFactory
{
    /**
     * Cria um cliente HTTP configurado com certificado mTLS
     *
     * @param TokenRequest $tokenRequest Requisição de token contendo certificado
     * @param int $timeout Timeout em segundos
     * @return Client Cliente HTTP configurado
     */
    public static function createFromTokenRequest(TokenRequest $tokenRequest, int $timeout = 30): Client
    {
        // Extrair certificado e chave do .pfx
        $pfxContent = file_get_contents($tokenRequest->getPfxPath());
        $certs = [];

        if (!openssl_pkcs12_read($pfxContent, $certs, $tokenRequest->getPassphrase())) {
            throw new \Exception('Failed to read PFX certificate');
        }

        // Criar arquivos temporários
        $certFile = tempnam(sys_get_temp_dir(), 'cert');
        $keyFile = tempnam(sys_get_temp_dir(), 'key');

        file_put_contents($certFile, $certs['cert']);
        file_put_contents($keyFile, $certs['pkey']);

        // Criar cliente com certificado configurado
        return new Client([
            'timeout' => $timeout,
            'connect_timeout' => 10,
            'cert' => [$certFile, $tokenRequest->getPassphrase()],
            'ssl_key' => $keyFile,
            'verify' => true,
            'http_errors' => false
        ]);
    }
}
