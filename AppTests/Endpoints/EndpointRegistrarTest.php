<?php

namespace Smolblog\App\Endpoint;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class ConcreteEndpointRegistrar extends EndpointRegistrar {
	protected function processConfig(EndpointConfig $config): string {
		return $config->route;
	}
}

final class TestEndpoint implements Endpoint {
	public static function config(): EndpointConfig {
		return new EndpointConfig(route: '/test/camelot');
	}

	public function run(EndpointRequest $request): EndpointResponse {
		return new EndpointResponse(body: ['sillyPlace' => true]);
	}
}

final class EndpointRegistrarTest extends TestCase {
	public function testItRegistersAnEndpoint() {
		$container = $this->createMock(ContainerInterface::class);
		$container->method('has')->willReturn(true);
		$container->expects($this->once())->method('get')->willReturn(new TestEndpoint());

		$registrar = new ConcreteEndpointRegistrar(container: $container);
		$registrar->register(TestEndpoint::class);

		$endpoint = $registrar->get('/test/camelot');
		$this->assertInstanceOf(TestEndpoint::class, $endpoint);
	}
}
