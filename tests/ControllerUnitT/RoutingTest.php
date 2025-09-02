<?php
declare(strict_types=1);

namespace App\Tests\ControllerUnitT;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoutingTest extends WebTestCase
{
	public function testApiRouteExists(): void
	{
		$client = static::createClient();

		// Проверяем, что маршрут существует
		$router = static::getContainer()->get('router');
		$route = $router->getRouteCollection()->get('insurance_calculate');

		$this->assertNotNull($route, 'Route insurance_calculate not found');
		$this->assertContains('POST', $route->getMethods());
	}

	public function testAllRoutes(): void
	{
		$client = static::createClient();
		$router = static::getContainer()->get('router');
		$routes = $router->getRouteCollection();

		foreach ($routes as $name => $route) {
			echo "Route: $name, Path: " . $route->getPath() . ", Methods: " . implode(',', $route->getMethods()) . "\n";
		}

		$this->assertTrue(count($routes) > 0, 'No routes found');
	}
}