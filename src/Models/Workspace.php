<?php

namespace Matfatjoe\SantanderBoleto\Models;

/**
 * Modelo de Workspace (resposta da API)
 */
class Workspace
{
    private $id;
    private $status;
    private $type;
    private $covenants;
    private $description;
    private $bankSlipBillingWebhookActive;
    private $pixBillingWebhookActive;
    private $webhookURL;
    private $createdAt;
    private $updatedAt;

    public function __construct(
        string $id,
        string $type,
        array $covenants,
        ?string $description = null,
        ?string $status = null,
        ?bool $bankSlipBillingWebhookActive = null,
        ?bool $pixBillingWebhookActive = null,
        ?string $webhookURL = null,
        ?string $createdAt = null,
        ?string $updatedAt = null
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->covenants = $covenants;
        $this->description = $description;
        $this->status = $status;
        $this->bankSlipBillingWebhookActive = $bankSlipBillingWebhookActive;
        $this->pixBillingWebhookActive = $pixBillingWebhookActive;
        $this->webhookURL = $webhookURL;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return Covenant[]
     */
    public function getCovenants(): array
    {
        return $this->covenants;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isBankSlipBillingWebhookActive(): ?bool
    {
        return $this->bankSlipBillingWebhookActive;
    }

    public function isPixBillingWebhookActive(): ?bool
    {
        return $this->pixBillingWebhookActive;
    }

    public function getWebhookURL(): ?string
    {
        return $this->webhookURL;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public static function fromArray(array $data): self
    {
        $covenants = [];
        if (isset($data['covenants'])) {
            foreach ($data['covenants'] as $covenantData) {
                $covenants[] = Covenant::fromArray($covenantData);
            }
        }

        return new self(
            $data['id'],
            $data['type'],
            $covenants,
            $data['description'] ?? null,
            $data['status'] ?? null,
            $data['bankSlipBillingWebhookActive'] ?? null,
            $data['pixBillingWebhookActive'] ?? null,
            $data['webhookURL'] ?? null,
            $data['createdAt'] ?? null,
            $data['updatedAt'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'type' => $this->type,
            'covenants' => array_map(fn($c) => $c->toArray(), $this->covenants),
            'description' => $this->description,
            'bankSlipBillingWebhookActive' => $this->bankSlipBillingWebhookActive,
            'pixBillingWebhookActive' => $this->pixBillingWebhookActive,
            'webhookURL' => $this->webhookURL,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}
