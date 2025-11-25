<?php

namespace Matfatjoe\SantanderBoleto\Boleto;

use Matfatjoe\SantanderBoleto\Models\Boleto;

/**
 * Request para registrar um novo boleto
 */
class RegisterBoletoRequest
{
    private $boleto;

    public function __construct(Boleto $boleto)
    {
        $this->boleto = $boleto;
    }

    public function getBoleto(): Boleto
    {
        return $this->boleto;
    }

    public function toArray(): array
    {
        return $this->boleto->toArray();
    }
}
