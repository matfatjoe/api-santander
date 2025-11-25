<?php

namespace Matfatjoe\SantanderBoleto\Models;

/**
 * Modelo de resposta do token de autenticação OAuth2
 * 
 * Representa a resposta completa da API de autenticação do Santander
 */
class Token
{
    private $accessToken;
    private $expiresIn;
    private $tokenType;
    private $notBeforePolicy;
    private $sessionState;
    private $scope;

    public function __construct(
        string $accessToken,
        int $expiresIn,
        string $tokenType = 'Bearer',
        ?int $notBeforePolicy = null,
        ?string $sessionState = null,
        ?string $scope = null
    ) {
        $this->accessToken = $accessToken;
        $this->expiresIn = $expiresIn;
        $this->tokenType = $tokenType;
        $this->notBeforePolicy = $notBeforePolicy;
        $this->sessionState = $sessionState;
        $this->scope = $scope;
    }

    /**
     * Obtém o token de acesso
     * 
     * @return string Token JWT para autenticação nas requisições
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    /**
     * Obtém o tempo de expiração em segundos
     * 
     * @return int Tempo em segundos até o token expirar (geralmente 900s = 15min)
     */
    public function getExpiresIn(): int
    {
        return $this->expiresIn;
    }

    /**
     * Obtém o tipo do token
     * 
     * @return string Tipo do token (normalmente "Bearer")
     */
    public function getTokenType(): string
    {
        return $this->tokenType;
    }

    /**
     * Obtém a política de início de validade
     * 
     * @return int|null Timestamp Unix indicando quando o token começa a ser válido
     */
    public function getNotBeforePolicy(): ?int
    {
        return $this->notBeforePolicy;
    }

    /**
     * Obtém o estado da sessão
     * 
     * @return string|null UUID da sessão de autenticação
     */
    public function getSessionState(): ?string
    {
        return $this->sessionState;
    }

    /**
     * Obtém os escopos autorizados
     * 
     * @return string|null Escopos de permissão do token
     */
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * Cria uma instância a partir de um array de dados da API
     * 
     * @param array $data Dados retornados pela API de autenticação
     * @return self Instância do modelo Token
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['access_token'],
            $data['expires_in'],
            $data['token_type'] ?? 'bearer',
            $data['not-before-policy'] ?? null,
            $data['session_state'] ?? null,
            $data['scope'] ?? null
        );
    }

    /**
     * Converte o token para array
     * 
     * @return array Representação em array do token
     */
    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'expires_in' => $this->expiresIn,
            'token_type' => $this->tokenType,
            'not-before-policy' => $this->notBeforePolicy,
            'session_state' => $this->sessionState,
            'scope' => $this->scope
        ];
    }

    /**
     * Verifica se o token está expirado (aproximadamente)
     * 
     * Nota: Esta é uma verificação aproximada baseada no tempo de criação do objeto.
     * Para verificação precisa, armazene o timestamp de criação separadamente.
     * 
     * @param int $bufferSeconds Margem de segurança em segundos (padrão: 60s)
     * @return bool True se o token provavelmente expirou
     */
    public function isExpired(int $bufferSeconds = 60): bool
    {
        // Esta é uma implementação básica
        // Em produção, você deveria armazenar o timestamp de criação
        return false; // Por enquanto sempre retorna false
    }

    /**
     * Retorna o header de autorização formatado
     * 
     * @return string Header no formato "Bearer {token}"
     */
    public function getAuthorizationHeader(): string
    {
        return ucfirst($this->tokenType) . ' ' . $this->accessToken;
    }
}
