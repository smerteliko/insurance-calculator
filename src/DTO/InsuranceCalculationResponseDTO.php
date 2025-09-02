<?php

declare(strict_types=1);

namespace App\DTO;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InsuranceCalculationResponse',
    description: 'Ответ с результатом расчета страховки'
)]
class InsuranceCalculationResponseDTO
{
    #[OA\Property(description: 'Общая стоимость в валюте страхования', example: 1.8)]
    private float $totalCostInCurrency;

    #[OA\Property(description: 'Общая стоимость в рублях', example: 144.0)]
    private float $totalCostInPreferredCurrency;

    #[OA\Property(description: 'Количество дней поездки', example: 3)]
    private int $daysCount;

    #[OA\Property(description: 'Коэффициент 1 дня', example: 0.6)]
    private float $dailyCoefficient;

    #[OA\Property(description: 'Курс валюты на сегодняшний день', example: 80.0)]
    private float $exchangeRate;

    #[OA\Property(description: 'Страховая сумма', example: 30000)]
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
