<?php

namespace Matfatjoe\SantanderBoleto\Models;

/**
 * Modelo de Multa (Fine)
 */
class Fine
{
    private $finePercentage;
    private $fineQuantityDays;

    public function __construct(
        string $finePercentage,
        string $fineQuantityDays
    ) {
        $this->finePercentage = $finePercentage;
        $this->fineQuantityDays = $fineQuantityDays;
    }

    public function getFinePercentage(): string
    {
        return $this->finePercentage;
    }

    public function getFineQuantityDays(): string
    {
        return $this->fineQuantityDays;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['finePercentage'] ?? '',
            $data['fineQuantityDays'] ?? ''
        );
    }

    public function toArray(): array
    {
        return [
            'finePercentage' => $this->finePercentage,
            'fineQuantityDays' => $this->fineQuantityDays
        ];
    }
}
