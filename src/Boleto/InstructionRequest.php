<?php

namespace Matfatjoe\SantanderBoleto\Boleto;

/**
 * Request para envio de instruções (PATCH)
 */
class InstructionRequest
{
    private $covenantCode;
    private $bankNumber;
    private $instructions;

    public function __construct(string $covenantCode, string $bankNumber, array $instructions)
    {
        $this->covenantCode = $covenantCode;
        $this->bankNumber = $bankNumber;
        $this->instructions = $instructions;
    }

    public function toArray(): array
    {
        $data = [
            'covenantCode' => $this->covenantCode,
            'bankNumber' => $this->bankNumber
        ];

        return array_merge($data, $this->instructions);
    }
}
