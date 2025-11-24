# Santander Boleto - Biblioteca PHP

Biblioteca PHP para emissão e gerenciamento de boletos bancários através da API do Santander.

## 📋 Requisitos

- PHP >= 7.4
- Composer
- Extensão OpenSSL habilitada
- Certificado digital .pfx fornecido pelo Santander

## 📦 Instalação

```bash
composer require matfatjoe/santander-boleto
```

## 🔐 Autenticação

A API do Santander utiliza autenticação mTLS (mutual TLS) com certificado digital. Você precisará de:

1. **Certificado .pfx**: Fornecido pelo Santander ao cadastrar sua aplicação
2. **Passphrase**: Senha do certificado
3. **Client ID**: Identificador da sua aplicação

### Exemplo de Autenticação

```php
<?php

require_once 'vendor/autoload.php';

use Matfatjoe\SantanderBoleto\Auth\Authenticator;
use Matfatjoe\SantanderBoleto\Auth\TokenRequest;
use GuzzleHttp\Client;

// Configuração
$pfxPath = '/path/to/certificate.pfx';
$passphrase = 'certificate_password';
$clientId = 'your_client_id';

// Criar cliente HTTP
$httpClient = new Client([
    'timeout' => 30,
    'connect_timeout' => 10
]);

// Criar autenticador
$authenticator = new Authenticator($httpClient);

// Obter token
$tokenRequest = new TokenRequest($pfxPath, $passphrase, $clientId);
$tokenResponse = $authenticator->getToken($tokenRequest);

echo "Access Token: " . $tokenResponse->getAccessToken() . "\n";
echo "Expira em: " . $tokenResponse->getExpiresIn() . " segundos\n";
```

## 🏗️ Estrutura do Projeto

```
src/
└── Auth/
    ├── Authenticator.php      # Gerencia autenticação OAuth2 com mTLS
    ├── TokenRequest.php       # Requisição de token
    └── TokenResponse.php      # Resposta com token de acesso

tests/
└── Auth/
    └── AuthenticatorTest.php  # Testes unitários de autenticação

examples/
└── example-auth.php           # Exemplo de autenticação
```

## 🔧 Funcionalidades Implementadas

- [x] **Autenticação OAuth2 com certificado mTLS**
  - Suporte a certificados .pfx
  - Extração automática de certificado e chave privada
  - Gerenciamento de token de acesso

## 📖 Resposta da API de Token

A API retorna os seguintes campos:

```json
{
  "access_token": "token_de_acesso",
  "expires_in": 900,
  "token_type": "bearer",
  "not-before-policy": 1614173461,
  "session_state": "aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaaa",
  "scope": ""
}
```

Todos os campos estão disponíveis através do objeto `TokenResponse`:

- `getAccessToken()`: Token de acesso para requisições
- `getExpiresIn()`: Tempo de expiração em segundos
- `getTokenType()`: Tipo do token (bearer)
- `getNotBeforePolicy()`: Política de início de validade
- `getSessionState()`: Estado da sessão
- `getScope()`: Escopos autorizados

## 🌐 Ambientes

### Sandbox (Testes)

```php
$authenticator = new Authenticator(
    $httpClient,
    'https://trust-sandbox.api.santander.com.br'
);
```

### Produção

```php
$authenticator = new Authenticator(
    $httpClient,
    'https://trust.api.santander.com.br'
);
```

## 🧪 Testes

Execute os testes unitários:

```bash
# Com Docker
docker-compose run --rm php vendor/bin/phpunit

# Ou com PHPUnit local
vendor/bin/phpunit
```

Saída esperada:

```
PHPUnit 9.6.29 by Sebastian Bergmann and contributors.

Authenticator (Matfatjoe\SantanderBoleto\Tests\Auth\Authenticator)
 ✔ Get token success
 ✔ Get token failure
 ✔ Invalid certificate path

OK (3 tests, 11 assertions)
```

## 🐳 Docker

O projeto inclui configuração Docker para desenvolvimento:

```bash
# Instalar dependências
docker-compose run --rm php composer install

# Rodar testes
docker-compose run --rm php vendor/bin/phpunit

# Executar exemplos
docker-compose run --rm php php examples/example-auth.php
```

## 🚧 Próximas Funcionalidades

As seguintes funcionalidades serão implementadas em breve:

- [ ] **Gerenciamento de Workspaces**
  - Criar, consultar, atualizar e deletar workspaces
- [ ] **Emissão de Boletos**
  - Registrar boletos
  - Enviar instruções (baixa, protesto, etc.)
- [ ] **Consultas**
  - Consulta simples por chave sonda
  - Consultas detalhadas com filtros

## 📝 Licença

Este projeto é de código aberto e está disponível sob a licença MIT.

## 🤝 Contribuindo

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou enviar pull requests.

## ⚠️ Segurança

- **NUNCA** commit certificados .pfx ou senhas no repositório
- Armazene certificados em local seguro fora do controle de versão
- Use variáveis de ambiente para configurações sensíveis
- O arquivo `.gitignore` já está configurado para excluir certificados

## 📞 Suporte

Para dúvidas sobre a API do Santander, consulte a documentação oficial ou entre em contato com o suporte técnico do banco.

---

**Desenvolvido para facilitar a integração com a API de Boletos do Santander**
