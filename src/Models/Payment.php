<?php

namespace Matfatjoe\SantanderBoleto\Models;

class Payment
{
    private $paidValue;
    private $interestValue;
    private $fineValue;
    private $deductionValue;
    private $rebateValue;
    private $iofValue;
    private $date;
    private $type;
    private $bankCode;
    private $channel;
    private $kind;
    private $txId;

    public function __construct(
        string $paidValue,
        ?string $interestValue = null,
        ?string $fineValue = null,
        ?string $deductionValue = null,
        ?string $rebateValue = null,
        ?string $iofValue = null,
        ?string $date = null,
        ?string $type = null,
        ?string $bankCode = null,
        ?string $channel = null,
        ?string $kind = null,
        ?string $txId = null
    ) {
        $this->paidValue = $paidValue;
        $this->interestValue = $interestValue;
        $this->fineValue = $fineValue;
        $this->deductionValue = $deductionValue;
        $this->rebateValue = $rebateValue;
        $this->iofValue = $iofValue;
        $this->date = $date;
        $this->type = $type;
        $this->bankCode = $bankCode;
        $this->channel = $channel;
        $this->kind = $kind;
        $this->txId = $txId;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['paidValue'] ?? '0',
            $data['interestValue'] ?? null,
            $data['fineValue'] ?? null,
            $data['deductionValue'] ?? null,
            $data['rebateValue'] ?? null,
            $data['iofValue'] ?? null,
            $data['date'] ?? null,
            $data['type'] ?? null,
            $data['bankCode'] ?? null,
            $data['channel'] ?? null,
            $data['kind'] ?? null,
            $data['txId'] ?? null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'paidValue' => $this->paidValue,
            'interestValue' => $this->interestValue,
            'fineValue' => $this->fineValue,
            'deductionValue' => $this->deductionValue,
            'rebateValue' => $this->rebateValue,
            'iofValue' => $this->iofValue,
            'date' => $this->date,
            'type' => $this->type,
            'bankCode' => $this->bankCode,
            'channel' => $this->channel,
            'kind' => $this->kind,
            'txId' => $this->txId,
        ], function ($value) {
            return $value !== null;
        });
    }

    public function getPaidValue(): string
    {
        return $this->paidValue;
    }
}