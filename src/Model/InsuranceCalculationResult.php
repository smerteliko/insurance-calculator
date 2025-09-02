<?php

declare(strict_types=1);

namespace App\Model;

class InsuranceCalculationResult
{
    private float $totalCostInCurrency;
    private float $totalCostInPreferredCurrency;
    private int $daysCount;
    private float $dailyCoefficient;
    private float $exchangeRate;
    private int $insuranceAmount;

    public function __construct(
        float $totalCostInCurrency,
        float $totalCostInPreferredCurrency,
        int $daysCount,
        float $dailyCoefficient,
        float $exchangeRate,
        int $insuranceAmount
    ) {
        $this->totalCostInCurrency = $totalCostInCurrency;
        $this->totalCostInPreferredCurrency = $totalCostInPreferredCurrency;
        $this->daysCount = $daysCount;
        $this->dailyCoefficient = $dailyCoefficient;
        $this->exchangeRate = $exchangeRate;
        $this->insuranceAmount = $insuranceAmount;
    }

    public function getTotalCostInCurrency(): float
    {
        return $this->totalCostInCurrency;
    }

    public function getTotalCostInPreferredCurrency(): float
    {
        return $this->totalCostInPreferredCurrency;
    }

    public function getDaysCount(): int
    {
        return $this->daysCount;
    }

    public function getDailyCoefficient(): float
    {
        return $this->dailyCoefficient;
    }

    public function getExchangeRate(): float
    {
        return $this->exchangeRate;
    }

    public function getInsuranceAmount(): int
    {
        return $this->insuranceAmount;
    }

    public function toArray(): array
    {
        return [
            'totalCostInCurrency' => round($this->totalCostInCurrency, 2),
            'totalCostInPreferredCurrency' => round($this->totalCostInPreferredCurrency, 2),
            'daysCount' => $this->daysCount,
            'dailyCoefficient' => round($this->dailyCoefficient, 2),
            'exchangeRate' => round($this->exchangeRate, 2),
            'insuranceAmount' => $this->insuranceAmount,
        ];
    }
}
