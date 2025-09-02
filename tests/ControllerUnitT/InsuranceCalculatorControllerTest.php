<?php

declare( strict_types=1 );

namespace App\Tests\ControllerUnitT;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class InsuranceCalculatorControllerTest extends WebTestCase {
	public function testCalculateSuccess(): void {
		$client = static::createClient();

		$data = [ 'insuranceAmount' => 30000,
		          'startDate'       => '2025-10-01',
		          'endDate'         => '2025-10-03',
		          'currencyCode'    => 'EUR' ];

		$client->jsonRequest('POST', '/api/insurance/calculate', $data);

		$this->assertResponseIsSuccessful();
		$this->assertResponseHeaderSame('Content-Type', 'application/json');

		$response = json_decode($client->getResponse()->getContent(), TRUE);

		$this->assertArrayHasKey('totalCostInCurrency', $response);
		$this->assertArrayHasKey('totalCostInPreferredCurrency', $response);
		$this->assertArrayHasKey('daysCount', $response);
		$this->assertArrayHasKey('dailyCoefficient', $response);
		$this->assertArrayHasKey('exchangeRate', $response);
		$this->assertArrayHasKey('insuranceAmount', $response);

		$this->assertEquals(1.8, $response['totalCostInCurrency']);
		$this->assertEquals(144.0, $response['totalCostInPreferredCurrency']);
		$this->assertEquals(3, $response['daysCount']);
		$this->assertEquals(0.6, $response['dailyCoefficient']);
		$this->assertEquals(80.0, $response['exchangeRate']);
		$this->assertEquals(30000, $response['insuranceAmount']);
	}

	public function testCalculateWithUSD(): void {
		$client = static::createClient();

		$data = [ 'insuranceAmount' => 50000,
		          'startDate'       => '2025-10-01',
		          'endDate'         => '2025-10-02',
		          'currencyCode'    => 'USD' ];

		$client->jsonRequest('POST', '/api/insurance/calculate', $data);

		$this->assertResponseIsSuccessful();

		$response = json_decode($client->getResponse()->getContent(), TRUE);

		$this->assertEquals(1.6, $response['totalCostInCurrency']);
		$this->assertEquals(112.0, $response['totalCostInPreferredCurrency']);
		$this->assertEquals(2, $response['daysCount']);
		$this->assertEquals(0.8, $response['dailyCoefficient']);
	}

	public function testInvalidInsuranceAmount(): void {
		$client = static::createClient();

		$data = [ 'insuranceAmount' => 40000,
		          'startDate'       => '2025-10-01',
		          'endDate'         => '2025-10-03',
		          'currencyCode'    => 'EUR' ];

		$client->jsonRequest('POST', '/api/insurance/calculate', $data);

		$this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
	}

	public function testInvalidCurrency(): void {
		$client = static::createClient();

		$data = [ 'insuranceAmount' => 30000,
		          'startDate'       => '2025-10-01',
		          'endDate'         => '2025-10-03',
		          'currencyCode'    => 'GBP' ];

		$client->jsonRequest('POST', '/api/insurance/calculate', $data);

		$this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
	}

	public function testInvalidDateRange(): void {
		$client = static::createClient();

		$data = [ 'insuranceAmount' => 30000,
		          'startDate'       => '2025-10-03',
		          'endDate'         => '2025-10-01',
		          'currencyCode'    => 'EUR' ];

		$client->jsonRequest('POST', '/api/insurance/calculate', $data);

		$this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
	}

	public function testInvalidJson(): void {
		$client = static::createClient();

		$client->request('POST',
		                 '/api/insurance/calculate',
		                 [],
		                 [],
		                 [ 'CONTENT_TYPE' => 'application/json' ],
		                 'invalid json');

		$this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
	}

	public function testMissingRequiredFields(): void {
		$client = static::createClient();

		$data = [ 'insuranceAmount' => 30000, // missing other required fields
		];

		$client->jsonRequest('POST', '/api/insurance/calculate', $data);

		$this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
	}
}