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
		'USD' => 70.0 // for testing
	];

	private static array $mutableDailyCoefficients = [
		30000 => 0.6,
		50000 => 0.8
	];

	private static array $mutableExchangeRates = [
		'EUR' => 80.0,
		'USD' => 70.0// for testing
	];

	/**
	 * @param InsuranceCalculationRequest $request
	 * @return InsuranceCalculationResult
	 * @throws \Exception
	 */
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

	/**
	 * @param InsuranceCalculationRequest $request
	 * @return void
	 * @throws \Exception
	 */
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

	/**
	 * @param \DateTimeInterface $startDate
	 * @param \DateTimeInterface $endDate
	 * @return int
	 */
	private function calculateDaysCount(\DateTimeInterface $startDate, \DateTimeInterface $endDate): int {
		$interval = $startDate->diff($endDate);
		return $interval->days + 1;
	}

	/**
	 * @param int $insuranceAmount
	 * @return float
	 */
	private function getDailyCoefficient(int $insuranceAmount): float {
		return self::DAILY_COEFFICIENTS[$insuranceAmount];
	}

	/**
	 * @param string $currencyCode
	 * @return float
	 */
	private function getExchangeRate(string $currencyCode): float {
		return self::EXCHANGE_RATES[$currencyCode];
	}

	/**
	 * For future
	 *
	 * @param int   $insuranceAmount
	 * @param float $coefficient
	 * @return void
	 */
	public function setDailyCoefficient(int $insuranceAmount, float $coefficient): void {
		self::$mutableDailyCoefficients[$insuranceAmount] = $coefficient;
	}

	/**
	 * For future
	 *
	 * @param string $currencyCode
	 * @param float  $rate
	 * @return void
	 */
	public function setExchangeRate(string $currencyCode, float $rate): void {
		self::$mutableExchangeRates[$currencyCode] = $rate;
	}

	/**
	 * For future
	 *
	 * @param int $insuranceAmount
	 * @return float
	 */
	public function getMutableDailyCoefficient(int $insuranceAmount): float {
		return self::$mutableDailyCoefficients[$insuranceAmount] ?? self::DAILY_COEFFICIENTS[$insuranceAmount];
	}

	/**
	 * For future
	 *
	 * @param string $currencyCode
	 * @return float
	 */
	public function getMutableExchangeRate(string $currencyCode): float {
		return self::$mutableExchangeRates[$currencyCode] ?? self::EXCHANGE_RATES[$currencyCode];
	}
}