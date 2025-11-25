<?php

namespace Matfatjoe\SantanderBoleto\Models;

/**
 * Modelo de Pagador (Payer)
 */
class Payer
{
    private $name;
    private $documentType;
    private $documentNumber;
    private $address;
    private $neighborhood;
    private $city;
    private $state;
    private $zipCode;

    public function __construct(
        string $name,
        string $documentType,
        string $documentNumber,
        string $address,
        string $neighborhood,
        string $city,
        string $state,
        string $zipCode
    ) {
        $this->name = $name;
        $this->documentType = $documentType;
        $this->documentNumber = $documentNumber;
        $this->address = $address;
        $this->neighborhood = $neighborhood;
        $this->city = $city;
        $this->state = $state;
        $this->zipCode = $zipCode;
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

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getNeighborhood(): string
    {
        return $this->neighborhood;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['documentType'],
            $data['documentNumber'],
            $data['address'],
            $data['neighborhood'],
            $data['city'],
            $data['state'],
            $data['zipCode']
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'documentType' => $this->documentType,
            'documentNumber' => $this->documentNumber,
            'address' => $this->address,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
            'zipCode' => $this->zipCode
        ];
    }
}
