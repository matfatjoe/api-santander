<?php

namespace Matfatjoe\SantanderBoleto\Workspace;

use Matfatjoe\SantanderBoleto\Models\Covenant;

/**
 * Requisição para criar um novo workspace
 */
class CreateWorkspaceRequest
{
    private $type;
    private $covenants;
    private $description;
    private $bankSlipBillingWebhookActive;
    private $pixBillingWebhookActive;
    private $webhookURL;

    /**
     * @param string $type Tipo do workspace (ex: "BILLING")
     * @param Covenant[] $covenants Lista de convênios
     * @param string|null $description Descrição do workspace
     * @param bool|null $bankSlipBillingWebhookActive Ativar webhook para boletos
     * @param bool|null $pixBillingWebhookActive Ativar webhook para PIX
     * @param string|null $webhookURL URL do webhook
     */
    public function __construct(
        string $type,
        array $covenants,
        ?string $description = null,
        ?bool $bankSlipBillingWebhookActive = null,
        ?bool $pixBillingWebhookActive = null,
        ?string $webhookURL = null
    ) {
        $this->type = $type;
        $this->covenants = $covenants;
        $this->description = $description;
        $this->bankSlipBillingWebhookActive = $bankSlipBillingWebhookActive;
        $this->pixBillingWebhookActive = $pixBillingWebhookActive;
        $this->webhookURL = $webhookURL;
    }

    public function toArray(): array
    {
        $data = [
            'type' => $this->type,
            'covenants' => array_map(fn($c) => $c->toArray(), $this->covenants)
        ];

        if ($this->description !== null) {
            $data['description'] = $this->description;
        }

        if ($this->bankSlipBillingWebhookActive !== null) {
            $data['bankSlipBillingWebhookActive'] = $this->bankSlipBillingWebhookActive;
        }

        if ($this->pixBillingWebhookActive !== null) {
            $data['pixBillingWebhookActive'] = $this->pixBillingWebhookActive;
        }

        if ($this->webhookURL !== null) {
            $data['webhookURL'] = $this->webhookURL;
        }

        return $data;
    }
}
