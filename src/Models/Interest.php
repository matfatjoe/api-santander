<?php

namespace Matfatjoe\SantanderBoleto\Models;

/**
 * Modelo de Juros (Interest)
 */
class Interest
{
    private $interestPercentage;

    public function __construct(string $interestPercentage)
    {
        $this->interestPercentage = $interestPercentage;
    }

    public function getInterestPercentage(): string
    {
        return $this->interestPercentage;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['interestPercentage'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'interestPercentage' => $this->interestPercentage
        ];
    }
}
