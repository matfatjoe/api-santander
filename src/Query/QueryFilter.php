<?php

namespace Matfatjoe\SantanderBoleto\Query;

/**
 * Filtros para consulta de boletos
 */
class QueryFilter
{
    private $status;
    private $limit;
    private $bankNumber;
    private $clientNumber;
    private $dueDateInitial;
    private $dueDateFinal;
    private $paymentDateInitial;
    private $paymentDateFinal;

    public function __construct(
        ?string $status = null,
        ?int $limit = null,
        ?string $bankNumber = null,
        ?string $clientNumber = null,
        ?string $dueDateInitial = null,
        ?string $dueDateFinal = null,
        ?string $paymentDateInitial = null,
        ?string $paymentDateFinal = null
    ) {
        $this->status = $status;
        $this->limit = $limit;
        $this->bankNumber = $bankNumber;
        $this->clientNumber = $clientNumber;
        $this->dueDateInitial = $dueDateInitial;
        $this->dueDateFinal = $dueDateFinal;
        $this->paymentDateInitial = $paymentDateInitial;
        $this->paymentDateFinal = $paymentDateFinal;
    }

    public function toQueryParams(): array
    {
        $params = [];

        if ($this->status) $params['status'] = $this->status;
        if ($this->limit) $params['_limit'] = $this->limit;
        if ($this->bankNumber) $params['bankNumber'] = $this->bankNumber;
        if ($this->clientNumber) $params['clientNumber'] = $this->clientNumber;
        if ($this->dueDateInitial) $params['dueDateInitial'] = $this->dueDateInitial;
        if ($this->dueDateFinal) $params['dueDateFinal'] = $this->dueDateFinal;
        if ($this->paymentDateInitial) $params['paymentDateInitial'] = $this->paymentDateInitial;
        if ($this->paymentDateFinal) $params['paymentDateFinal'] = $this->paymentDateFinal;

        return $params;
    }

    // Getters
    public function getStatus(): ?string
    {
        return $this->status;
    }
    public function getLimit(): ?int
    {
        return $this->limit;
    }
    public function getBankNumber(): ?string
    {
        return $this->bankNumber;
    }
    public function getClientNumber(): ?string
    {
        return $this->clientNumber;
    }
    public function getDueDateInitial(): ?string
    {
        return $this->dueDateInitial;
    }
    public function getDueDateFinal(): ?string
    {
        return $this->dueDateFinal;
    }
    public function getPaymentDateInitial(): ?string
    {
        return $this->paymentDateInitial;
    }
    public function getPaymentDateFinal(): ?string
    {
        return $this->paymentDateFinal;
    }
}
