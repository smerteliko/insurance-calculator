<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\InsuranceCalculationRequest;
use App\Model\InsuranceCalculationResult;

class InsuranceCalculatorService {
	private const DAILY_COEFFICIENTS = [
		30000 => 0.6,
		50000 => 0.8
	];

	private const EXCHANGE_RATES = [
		'EUR' => 80.0,
		'USD' => 70.0
	];

	// Для изменяемых коэффициентов используем обычные свойства
	private static array $mutableDailyCoefficients = [
		30000 => 0.6,
		50000 => 0.8
	];

	private static array $mutableExchangeRates = [
		'EUR' => 80.0,
		'USD' => 70.0
	];

	public function calculate(InsuranceCalculationRequest $request): InsuranceCalculationResult {
		$this->validateRequest($request);

		$daysCount = $this->calculateDaysCount(
			new \DateTime($request->getStartDate()),
			new \DateTime($request->getEndDate())
		);

		$dailyCoefficient = $this->getDailyCoefficient($request->getInsuranceAmount());
		$exchangeRate = $this->getExchangeRate($request->getCurrencyCode());

		$totalCostInCurrency = $dailyCoefficient * $daysCount;
		$totalCostInRubles = $totalCostInCurrency * $exchangeRate;

		return new InsuranceCalculationResult(
			$totalCostInCurrency,
			$totalCostInRubles,
			$daysCount,
			$dailyCoefficient,
			$exchangeRate,
			$request->getInsuranceAmount()
		);
	}

	private function validateRequest(InsuranceCalculationRequest $request): void {
		if (!array_key_exists($request->getInsuranceAmount(), self::DAILY_COEFFICIENTS)) {
			throw new \InvalidArgumentException('Invalid insurance amount');
		}

		if (!array_key_exists($request->getCurrencyCode(), self::EXCHANGE_RATES)) {
			throw new \InvalidArgumentException('Invalid currency code');
		}

		$startDate = new \DateTime($request->getStartDate());
		$endDate = new \DateTime($request->getEndDate());

		if ($endDate <= $startDate) {
			throw new \InvalidArgumentException('End date must be after start date');
		}
	}

	private function calculateDaysCount(\DateTimeInterface $startDate, \DateTimeInterface $endDate): int {
		$interval = $startDate->diff($endDate);
		return $interval->days + 1;
	}

	private function getDailyCoefficient(int $insuranceAmount): float {
		return self::DAILY_COEFFICIENTS[$insuranceAmount];
	}

	private function getExchangeRate(string $currencyCode): float {
		return self::EXCHANGE_RATES[$currencyCode];
	}

	public function setDailyCoefficient(int $insuranceAmount, float $coefficient): void {
		self::$mutableDailyCoefficients[$insuranceAmount] = $coefficient;
	}

	public function setExchangeRate(string $currencyCode, float $rate): void {
		self::$mutableExchangeRates[$currencyCode] = $rate;
	}

	public function getMutableDailyCoefficient(int $insuranceAmount): float {
		return self::$mutableDailyCoefficients[$insuranceAmount] ?? self::DAILY_COEFFICIENTS[$insuranceAmount];
	}

	public function getMutableExchangeRate(string $currencyCode): float {
		return self::$mutableExchangeRates[$currencyCode] ?? self::EXCHANGE_RATES[$currencyCode];
	}
}