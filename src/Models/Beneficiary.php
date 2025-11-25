<?php

namespace Matfatjoe\SantanderBoleto\Models;

/**
 * Modelo de Beneficiário (Beneficiary)
 */
class Beneficiary
{
    private $name;
    private $documentType;
    private $documentNumber;

    public function __construct(
        string $name,
        string $documentType,
        string $documentNumber
    ) {
        $this->name = $name;
        $this->documentType = $documentType;
        $this->documentNumber = $documentNumber;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDocumentType(): string
    {
        return $this->documentType;
    }

    public function getDocumentNumber(): string
    {
        return $this->documentNumber;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['documentType'],
            $data['documentNumber']
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'documentType' => $this->documentType,
            'documentNumber' => $this->documentNumber
        ];
    }
}
