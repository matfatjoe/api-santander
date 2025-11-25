<?php

namespace Matfatjoe\SantanderBoleto\Models;

/**
 * Modelo de Desconto (Discount)
 */
class Discount
{
    private $type;
    private $discountOne;
    private $discountTwo;
    private $discountThree;

    public function __construct(
        string $type,
        ?array $discountOne = null,
        ?array $discountTwo = null,
        ?array $discountThree = null
    ) {
        $this->type = $type;
        $this->discountOne = $discountOne;
        $this->discountTwo = $discountTwo;
        $this->discountThree = $discountThree;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDiscountOne(): ?array
    {
        return $this->discountOne;
    }

    public function getDiscountTwo(): ?array
    {
        return $this->discountTwo;
    }

    public function getDiscountThree(): ?array
    {
        return $this->discountThree;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['type'] ?? '',
            $data['discountOne'] ?? null,
            $data['discountTwo'] ?? null,
            $data['discountThree'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'discountOne' => $this->discountOne,
            'discountTwo' => $this->discountTwo,
            'discountThree' => $this->discountThree
        ];
    }
}
