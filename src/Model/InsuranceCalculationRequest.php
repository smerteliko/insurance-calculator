<?php

declare(strict_types=1);

namespace App\Model;

class InsuranceCalculationRequest
{
    private int $insuranceAmount;
    private string $startDate;
    private string $endDate;
    private string $currencyCode;

    public function __construct(int $insuranceAmount, string $startDate, string $endDate, string $currencyCode)
    {
        $this->insuranceAmount = $insuranceAmount;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->currencyCode = $currencyCode;
    }

    public function getInsuranceAmount(): int
    {
        return $this->insuranceAmount;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }
}
