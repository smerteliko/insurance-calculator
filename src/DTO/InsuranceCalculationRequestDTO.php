<?php

declare(strict_types=1);

namespace App\DTO;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InsuranceCalculationRequestDTO',
    description: 'Запрос на расчет страховки',
    required: [
        'insuranceAmount',
        'startDate',
        'endDate',
        'currencyCode']
)]
class InsuranceCalculationRequestDTO
{
    #[OA\Property(
        description: 'Страховая сумма',
        enum: [30000, 50000],
        example: 30000
    )]
    private int $insuranceAmount;

    #[OA\Property(
        description: 'Дата начала поездки (YYYY-MM-DD)',
        example: '2025-10-01'
    )]
    private string $startDate;

    #[OA\Property(
        description: 'Дата окончания поездки (YYYY-MM-DD)',
        example: '2025-10-03'
    )]
    private string $endDate;

    #[OA\Property(
        description: 'Код валюты (3 символа)',
        enum: ['EUR', 'USD'],
        example: 'EUR'
    )]
    private string $currencyCode;

    public function __construct(
        int $insuranceAmount,
        string $startDate,
        string $endDate,
        string $currencyCode
    ) {
        $this->insuranceAmount = $insuranceAmount;
        $this->startDate       = $startDate;
        $this->endDate         = $endDate;
        $this->currencyCode    = $currencyCode;
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
