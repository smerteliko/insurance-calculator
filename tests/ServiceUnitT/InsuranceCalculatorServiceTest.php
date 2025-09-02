<?php
declare(strict_types=1);

namespace App\Tests\ServiceUnitT;

use App\Model\InsuranceCalculationRequest;
use App\Service\InsuranceCalculatorService;
use PHPUnit\Framework\TestCase;

class InsuranceCalculatorServiceTest extends TestCase
{
	private InsuranceCalculatorService $service;

	protected function setUp(): void
	{
		$this->service = new InsuranceCalculatorService();
	}

	public function testCalculateWithEUR(): void
	{
		$request = new InsuranceCalculationRequest(
			30000,
			'2025-10-01',
			'2025-10-03',
			'EUR'
		);

		$result = $this->service->calculate($request);

		$this->assertEqualsWithDelta(1.8, $result->getTotalCostInCurrency(), 0.0001);
		$this->assertEqualsWithDelta(144.0, $result->getTotalCostInPreferredCurrency(), 0.0001);
		$this->assertEquals(3, $result->getDaysCount());
		$this->assertEqualsWithDelta(0.6, $result->getDailyCoefficient(), 0.0001);
		$this->assertEqualsWithDelta(80.0, $result->getExchangeRate(), 0.0001);
		$this->assertEquals(30000, $result->getInsuranceAmount());
	}

	public function testCalculateWithUSD(): void
	{
		$request = new InsuranceCalculationRequest(
			50000,
			'2025-10-01',
			'2025-10-02',
			'USD'
		);

		$result = $this->service->calculate($request);

		$this->assertEquals(1.6, $result->getTotalCostInCurrency());
		$this->assertEquals(112.0, $result->getTotalCostInPreferredCurrency());
		$this->assertEquals(2, $result->getDaysCount());
		$this->assertEquals(0.8, $result->getDailyCoefficient());
		$this->assertEquals(70.0, $result->getExchangeRate());
	}

	public function testInvalidInsuranceAmount(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid insurance amount');

		$request = new InsuranceCalculationRequest(
			40000,
			'2025-10-01',
			'2025-10-03',
			'EUR'
		);

		$this->service->calculate($request);
	}

	public function testInvalidCurrency(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('Invalid currency code');

		$request = new InsuranceCalculationRequest(
			30000,
			'2025-10-01',
			'2025-10-03',
			'GBP'
		);

		$this->service->calculate($request);
	}

	public function testInvalidDateRange(): void
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('End date must be after start date');

		$request = new InsuranceCalculationRequest(
			30000,
			'2025-10-03',
			'2025-10-01',
			'EUR'
		);

		$this->service->calculate($request);
	}

	public function testCalculateDaysCount(): void
	{
		$method = new \ReflectionMethod(InsuranceCalculatorService::class, 'calculateDaysCount');

		$startDate = new \DateTime('2025-10-01');
		$endDate = new \DateTime('2025-10-03');

		$daysCount = $method->invoke($this->service, $startDate, $endDate);

		$this->assertEquals(3, $daysCount);
	}

	public function testGetDailyCoefficient(): void
	{
		$method = new \ReflectionMethod(InsuranceCalculatorService::class, 'getDailyCoefficient');

		$coefficient30000 = $method->invoke($this->service, 30000);
		$coefficient50000 = $method->invoke($this->service, 50000);

		$this->assertEquals(0.6, $coefficient30000);
		$this->assertEquals(0.8, $coefficient50000);
	}

	public function testGetExchangeRate(): void
	{
		$method = new \ReflectionMethod(InsuranceCalculatorService::class, 'getExchangeRate');

		$rateEUR = $method->invoke($this->service, 'EUR');
		$rateUSD = $method->invoke($this->service, 'USD');

		$this->assertEquals(80.0, $rateEUR);
		$this->assertEquals(70.0, $rateUSD);
	}
}