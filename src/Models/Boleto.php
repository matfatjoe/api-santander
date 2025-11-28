<?php

namespace Matfatjoe\SantanderBoleto\Models;

/**
 * Modelo de Boleto Bancário (Bank Slip)
 */
class Boleto
{
    private $environment;
    private $nsuCode;
    private $nsuDate;
    private $covenantCode;
    private $bankNumber;
    private $clientNumber;
    private $dueDate;
    private $issueDate;
    private $participantCode;
    private $nominalValue;
    private $payer; // Nullable
    private $beneficiary; // Nullable
    private $discount;
    private $fine;
    private $interest;
    private $writeOffQuantityDays;
    private $paymentType;
    private $documentKind;
    private $deductionValue;
    private $barcode;
    private $digitableLine;
    private $qrCodePix;
    private $qrCodeUrl;
    private $messages;

    public function __construct(
        string $environment,
        string $nsuCode,
        string $nsuDate,
        string $covenantCode,
        string $bankNumber,
        string $clientNumber,
        string $dueDate,
        string $issueDate,
        string $participantCode,
        string $nominalValue,
        ?Payer $payer = null,
        ?Beneficiary $beneficiary = null,
        ?Discount $discount = null,
        ?Fine $fine = null,
        ?Interest $interest = null,
        ?string $writeOffQuantityDays = null,
        string $paymentType = 'REGISTRO',
        string $documentKind = 'DUPLICATA_MERCANTIL',
        ?string $deductionValue = null,
        ?string $barcode = null,
        ?string $digitableLine = null,
        ?string $qrCodePix = null,
        ?string $qrCodeUrl = null,
        array $messages = []
    ) {
        $this->environment = $environment;
        $this->nsuCode = $nsuCode;
        $this->nsuDate = $nsuDate;
        $this->covenantCode = $covenantCode;
        $this->bankNumber = $bankNumber;
        $this->clientNumber = $clientNumber;
        $this->dueDate = $dueDate;
        $this->issueDate = $issueDate;
        $this->participantCode = $participantCode;
        $this->nominalValue = $nominalValue;
        $this->payer = $payer;
        $this->beneficiary = $beneficiary;
        $this->discount = $discount;
        $this->fine = $fine;
        $this->interest = $interest;
        $this->writeOffQuantityDays = $writeOffQuantityDays;
        $this->paymentType = $paymentType;
        $this->documentKind = $documentKind;
        $this->deductionValue = $deductionValue;
        $this->barcode = $barcode;
        $this->digitableLine = $digitableLine;
        $this->qrCodePix = $qrCodePix;
        $this->qrCodeUrl = $qrCodeUrl;
        $this->messages = $messages;
    }

    // Getters
    public function getEnvironment(): string
    {
        return $this->environment;
    }
    public function getNsuCode(): string
    {
        return $this->nsuCode;
    }
    public function getNsuDate(): string
    {
        return $this->nsuDate;
    }
    public function getCovenantCode(): string
    {
        return $this->covenantCode;
    }
    public function getBankNumber(): string
    {
        return $this->bankNumber;
    }
    public function getClientNumber(): string
    {
        return $this->clientNumber;
    }
    public function getDueDate(): string
    {
        return $this->dueDate;
    }
    public function getIssueDate(): string
    {
        return $this->issueDate;
    }
    public function getParticipantCode(): string
    {
        return $this->participantCode;
    }
    public function getNominalValue(): string
    {
        return $this->nominalValue;
    }
    public function getPayer(): ?Payer
    {
        return $this->payer;
    }
    public function getBeneficiary(): ?Beneficiary
    {
        return $this->beneficiary;
    }
    public function getDiscount(): ?Discount
    {
        return $this->discount;
    }
    public function getFine(): ?Fine
    {
        return $this->fine;
    }
    public function getInterest(): ?Interest
    {
        return $this->interest;
    }
    public function getWriteOffQuantityDays(): ?string
    {
        return $this->writeOffQuantityDays;
    }
    public function getPaymentType(): string
    {
        return $this->paymentType;
    }
    public function getDocumentKind(): string
    {
        return $this->documentKind;
    }
    public function getDeductionValue(): ?string
    {
        return $this->deductionValue;
    }
    public function getBarcode(): ?string
    {
        return $this->barcode;
    }
    public function getDigitableLine(): ?string
    {
        return $this->digitableLine;
    }
    public function getQrCodePix(): ?string
    {
        return $this->qrCodePix;
    }
    public function getQrCodeUrl(): ?string
    {
        return $this->qrCodeUrl;
    }
    public function getMessages(): array
    {
        return $this->messages;
    }

    public static function fromArray(array $data): self
    {
        // Mapeamento de dados aninhados (bankslipData)
        if (isset($data['bankslipData'])) {
            $bsData = $data['bankslipData'];
            $data['barcode'] = $bsData['barCode'] ?? $data['barcode'] ?? null;
            $data['digitableLine'] = $bsData['digitableLine'] ?? $data['digitableLine'] ?? null;
            // Outros campos que podem estar em bankslipData
            $data['paymentType'] = $bsData['paymentType'] ?? $data['paymentType'] ?? 'REGISTRO';
            $data['documentKind'] = $bsData['documentKind'] ?? $data['documentKind'] ?? 'DUPLICATA_MERCANTIL';
        }

        // Mapeamento de payerData para estrutura do Payer
        if (isset($data['payerData'])) {
            $pData = $data['payerData'];
            $data['payer'] = [
                'name' => $pData['payerName'] ?? '',
                'documentType' => $pData['payerDocumentType'] ?? '',
                'documentNumber' => $pData['payerDocumentNumber'] ?? '',
                'address' => $pData['payerAddress'] ?? '',
                'neighborhood' => $pData['payerNeighborhood'] ?? '',
                'city' => $pData['payerCounty'] ?? '',
                'state' => $pData['payerStateAbbreviation'] ?? '',
                'zipCode' => $pData['payerZipCode'] ?? ''
            ];
        }

        return new self(
            $data['environment'] ?? '',
            $data['nsuCode'] ?? '',
            $data['nsuDate'] ?? '',
            $data['covenantCode'] ?? '',
            $data['bankNumber'] ?? '',
            $data['clientNumber'] ?? '',
            $data['dueDate'] ?? '',
            $data['issueDate'] ?? '',
            $data['participantCode'] ?? '',
            $data['nominalValue'] ?? '',
            isset($data['payer']) && is_array($data['payer']) ? Payer::fromArray($data['payer']) : null,
            isset($data['beneficiary']) && is_array($data['beneficiary']) ? Beneficiary::fromArray($data['beneficiary']) : null,
            isset($data['discount']) ? Discount::fromArray($data['discount']) : null,
            isset($data['finePercentage']) ? new Fine($data['finePercentage'], $data['fineQuantityDays'] ?? '') : null,
            isset($data['interestPercentage']) ? new Interest($data['interestPercentage']) : null,
            $data['writeOffQuantityDays'] ?? null,
            $data['paymentType'] ?? 'REGISTRO',
            $data['documentKind'] ?? 'DUPLICATA_MERCANTIL',
            $data['deductionValue'] ?? null,
            $data['barcode'] ?? null,
            $data['digitableLine'] ?? null,
            $data['qrCodePix'] ?? null,
            $data['qrCodeUrl'] ?? null,
            $data['messages'] ?? []
        );
    }

    public function toArray(): array
    {
        $data = [
            'environment' => $this->environment,
            'nsuCode' => $this->nsuCode,
            'nsuDate' => $this->nsuDate,
            'covenantCode' => $this->covenantCode,
            'bankNumber' => $this->bankNumber,
            'clientNumber' => $this->clientNumber,
            'dueDate' => $this->dueDate,
            'issueDate' => $this->issueDate,
            'participantCode' => $this->participantCode,
            'nominalValue' => $this->nominalValue,
            'paymentType' => $this->paymentType,
            'documentKind' => $this->documentKind,
            'messages' => $this->messages
        ];

        if ($this->payer) {
            $data['payer'] = $this->payer->toArray();
        }

        if ($this->beneficiary) {
            $data['beneficiary'] = $this->beneficiary->toArray();
        }

        if ($this->discount) {
            $data['discount'] = $this->discount->toArray();
        }

        if ($this->fine) {
            $data['finePercentage'] = $this->fine->getFinePercentage();
            $data['fineQuantityDays'] = $this->fine->getFineQuantityDays();
        }

        if ($this->interest) {
            $data['interestPercentage'] = $this->interest->getInterestPercentage();
        }

        if ($this->writeOffQuantityDays) {
            $data['writeOffQuantityDays'] = $this->writeOffQuantityDays;
        }

        if ($this->deductionValue) {
            $data['deductionValue'] = $this->deductionValue;
        }

        if ($this->barcode) $data['barcode'] = $this->barcode;
        if ($this->digitableLine) $data['digitableLine'] = $this->digitableLine;
        if ($this->qrCodePix) $data['qrCodePix'] = $this->qrCodePix;
        if ($this->qrCodeUrl) $data['qrCodeUrl'] = $this->qrCodeUrl;

        return $data;
    }
}
