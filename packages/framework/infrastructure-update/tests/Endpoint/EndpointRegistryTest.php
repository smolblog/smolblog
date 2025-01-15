<?php

namespace Smolblog\Infrastructure\Endpoint;

use PHPUnit\Framework\MockObject\MockObject;
use Smolblog\Infrastructure\Model;
use Smolblog\Test\AppTest;

abstract class EndpointOne implements Endpoint {
	public static function getConfiguration(): EndpointConfiguration {
		return new EndpointConfiguration(route: '/test/one');
	}
}
abstract class EndpointTwo implements Endpoint {
	public static function getConfiguration(): EndpointConfiguration {
		return new EndpointConfiguration(route: '/test/two');
	}
}
abstract class EndpointThree implements Endpoint {
	public static function getConfiguration(): EndpointConfiguration {
		return new EndpointConfiguration(route: '/test/three', key: 'TestEndpointThree');
	}
}

final class EndpointRegistryTest extends AppTest {
	const INCLUDED_MODELS = [Model::class];

	private MockObject & EndpointOne $endpointOne;
	private MockObject & EndpointTwo $endpointTwo;
	private MockObject & EndpointThree $endpointThree;

	protected function createMockServices(): array {
		$this->endpointOne = $this->createMock(EndpointOne::class);
		$this->endpointTwo = $this->createMock(EndpointTwo::class);
		$this->endpointThree = $this->createMock(EndpointThree::class);

		return [
			EndpointOne::class => fn() => $this->endpointOne,
			EndpointTwo::class => fn() => $this->endpointTwo,
			EndpointThree::class => fn() => $this->endpointThree,
		];
	}

	public function testItCatalogsAllAvailableEndpoints() {
		$service = $this->app->container->get(EndpointRegistry::class);
		$expected = [
			'GET /test/one' => new EndpointConfiguration(route: '/test/one'),
			'GET /test/two' => new EndpointConfiguration(route: '/test/two'),
			'TestEndpointThree' => new EndpointConfiguration(route: '/test/three', key: 'TestEndpointThree'),
		];

		$this->assertEquals($expected, $service->getEndpointConfigurations());
		$this->assertInstanceOf(EndpointOne::class, $service->getService('GET /test/one'));
		$this->assertInstanceOf(EndpointTwo::class, $service->getService('GET /test/two'));
		$this->assertInstanceOf(EndpointThree::class, $service->getService('TestEndpointThree'));
	}
}
