<?php

namespace Matfatjoe\SantanderBoleto\Models;

/**
 * Modelo de dados de convênio
 */
class Covenant
{
    private $code;

    /**
     * @param string|int $code
     */
    public function __construct($code)
    {
        $this->code = (string) $code;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self($data['code']);
    }
}
