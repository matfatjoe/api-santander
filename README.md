# Santander Boleto API - PHP Library

[![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

Biblioteca PHP para integração com a API de Cobrança do Santander, permitindo o gerenciamento completo de boletos bancários e workspaces.

## 📋 Índice

- [Características](#-características)
- [Requisitos](#-requisitos)
- [Instalação](#-instalação)
- [Configuração](#-configuração)
- [Uso Básico](#-uso-básico)
- [Módulos](#-módulos)
- [Exemplos](#-exemplos)
- [Testes](#-testes)
- [Documentação da API](#-documentação-da-api)
- [Contribuindo](#-contribuindo)
- [Licença](#-licença)

## ✨ Características

- ✅ **Autenticação mTLS** - Suporte completo a certificados digitais
- ✅ **Gerenciamento de Workspaces** - CRUD completo de workspaces
- ✅ **Registro de Boletos** - Emissão de boletos com todos os campos suportados
- ✅ **Consultas Avançadas** - Múltiplos métodos de consulta (SONDA, por nosso número, por seu número, lista)
- ✅ **Instruções de Boleto** - Envio de comandos (descontos, multas, baixas, etc.)
- ✅ **Testes Unitários** - 17 testes, 46 asserções
- ✅ **PSR-4 Autoloading** - Estrutura moderna e organizada
- ✅ **Type Hints** - Código fortemente tipado para PHP 7.4+

## 📦 Requisitos

- PHP >= 7.4
- Composer
- Extensões PHP:
  - `ext-json`
  - `ext-openssl`
  - `ext-curl`
- Certificado digital PFX do Santander
- Credenciais da API (Client ID e Client Secret)

## 🚀 Instalação

```bash
composer require matfatjoe/api-santander
```

Ou clone o repositório:

```bash
git clone https://github.com/matfatjoe/api-santander.git
cd api-santander
composer install
```

## ⚙️ Configuração

### 1. Certificado Digital

Coloque seu certificado `.pfx` no diretório do projeto e configure as credenciais:

```php
$pfxPath = __DIR__ . '/certificate.pfx';
$passphrase = 'sua_senha_do_certificado';
$clientId = 'seu_client_id';
$clientSecret = 'seu_client_secret';
```

### 2. Ambiente

```php
// Sandbox (Testes)
$baseUrl = 'https://trust-sandbox.api.santander.com.br';

// Produção
$baseUrl = 'https://trust-open.api.santander.com.br';
```

## 💡 Uso Básico

### Autenticação

```php
use Matfatjoe\SantanderBoleto\Auth\Authenticator;
use Matfatjoe\SantanderBoleto\Auth\TokenRequest;
use Matfatjoe\SantanderBoleto\HttpClientFactory;

$tokenRequest = new TokenRequest($pfxPath, $passphrase, $clientId, $clientSecret);
$httpClient = HttpClientFactory::createFromTokenRequest($tokenRequest);
$authenticator = new Authenticator($httpClient, $baseUrl);
$token = $authenticator->getToken($tokenRequest);
```

### Criar Workspace

```php
use Matfatjoe\SantanderBoleto\Workspace\WorkspaceService;
use Matfatjoe\SantanderBoleto\Workspace\CreateWorkspaceRequest;
use Matfatjoe\SantanderBoleto\Models\Covenant;

$workspaceService = new WorkspaceService($httpClient, $token, $clientId, $baseUrl);

$request = new CreateWorkspaceRequest(
    'BILLING',
    [new Covenant('3567206')],
    'Meu Workspace',
    true,  // Webhook boleto ativo
    true,  // Webhook PIX ativo
    'https://meu-site.com/webhook'
);

$workspace = $workspaceService->create($request);
echo "Workspace criado: " . $workspace->getId();
```

### Registrar Boleto

```php
use Matfatjoe\SantanderBoleto\Boleto\BoletoService;
use Matfatjoe\SantanderBoleto\Boleto\RegisterBoletoRequest;
use Matfatjoe\SantanderBoleto\Models\Boleto;
use Matfatjoe\SantanderBoleto\Models\Payer;
use Matfatjoe\SantanderBoleto\Models\Beneficiary;

$boletoService = new BoletoService($httpClient, $token, $clientId, $baseUrl);

$payer = new Payer(
    'João da Silva',
    'CPF',
    '12345678901',
    'Rua das Flores, 123',
    'Centro',
    'São Paulo',
    'SP',
    '01234-567'
);

$beneficiary = new Beneficiary(
    'Minha Empresa LTDA',
    'CNPJ',
    '12345678000199'
);

$boleto = new Boleto(
    'TESTE',
    '1014',
    date('Y-m-d'),
    '3567206',
    '000001',
    'CLI-001',
    date('Y-m-d', strtotime('+7 days')),
    date('Y-m-d'),
    'VENDA-001',
    '150.00',
    $payer,
    $beneficiary
);

$registeredBoleto = $boletoService->register($workspaceId, new RegisterBoletoRequest($boleto));
echo "Código de Barras: " . $registeredBoleto->getBarcode();
```

### Consultar Boleto

```php
use Matfatjoe\SantanderBoleto\Query\QueryService;

$queryService = new QueryService($httpClient, $token, $clientId, $baseUrl);

// Por Nosso Número
$boleto = $queryService->queryByBankNumber('3567206', '000001', 'default');

// Por Seu Número
$boleto = $queryService->queryByClientNumber('3567206', 'CLI-001', '2024-01-01', '150.00');

// SONDA (confirma registro - até D+2)
$boleto = $queryService->querySonda($workspaceId, '1014', '2024-01-01', 'TESTE', '3567206', '000001');
```

## 📚 Módulos

### 🔐 Auth Module

- `Authenticator` - Autenticação mTLS
- `TokenRequest` / `TokenResponse` - Gerenciamento de tokens
- `HttpClientFactory` - Cliente HTTP configurado

### 🏢 Workspace Module

- `WorkspaceService` - CRUD de workspaces
- `CreateWorkspaceRequest` / `UpdateWorkspaceRequest` - Requests
- `Workspace` / `Covenant` - Models

### 📄 Boleto Module

- `BoletoService` - Registro e instruções
- `RegisterBoletoRequest` / `InstructionRequest` - Requests
- `Boleto` / `Payer` / `Beneficiary` / `Discount` / `Fine` / `Interest` - Models

### 🔍 Query Module

- `QueryService` - Consultas de boletos
- `QueryFilter` - Filtros de consulta
- Métodos: SONDA, por nosso número, por seu número, lista

## 📖 Exemplos

Veja a pasta `examples/` para exemplos completos:

- [`example-auth.php`](examples/example-auth.php) - Autenticação
- [`example-workspace.php`](examples/example-workspace.php) - Gerenciamento de workspaces
- [`example-boleto.php`](examples/example-boleto.php) - Registro de boletos
- [`example-query.php`](examples/example-query.php) - Consultas

## 🧪 Testes

Execute os testes unitários:

```bash
composer test
```

Ou com Docker:

```bash
docker-compose run --rm php vendor/bin/phpunit --testdox
```

**Cobertura atual:** 17 testes, 46 asserções ✅

## 📘 Documentação da API

- [Portal do Desenvolvedor Santander](https://developer.santander.com.br/)

## 🤝 Contribuindo

Contribuições são bem-vindas! Por favor:

1. Fork o projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Add: Minha nova feature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

## 🆘 Suporte

- 🐛 Issues: [GitHub Issues](https://github.com/matfatjoe/api-santander/issues)

---

**Desenvolvido por [Matheus Furquim de Camargo](https://github.com/matfatjoe)**
