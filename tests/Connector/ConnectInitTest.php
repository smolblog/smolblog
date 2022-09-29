<?php

namespace Smolblog\Core\Endpoints;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\{Connector, ConnectorInitData, EndpointRequest, EndpointResponse, Environment};
use Smolblog\Core\Factories\TransientFactory;
use Smolblog\Core\Registrars\ConnectorRegistrar;
use Smolblog\Test\EndpointTestToolkit;

final class ConnectInitTest extends TestCase {
	use EndpointTestToolkit;

	public function setUp(): void {
		$environment = new Environment(apiBase: 'https://smol.blog/api');

		$connector = $this->createStub(Connector::class);
		$connector->method('getInitializationData')->willReturn(new ConnectorInitData(url: '//', state: 'bob', info: []));

		$connectors = $this->createStub(ConnectorRegistrar::class);
		$connectors->method('retrieve')->willReturn($connector);

		$transients = $this->createStub(TransientFactory::class);
		$transients->method('getTransient')->willReturn(['thing'=>'one']);

		$this->endpoint = new ConnectInit(
			env: $environment,
			connectors: $connectors,
			transients: $transients,
		);
	}

	public function testItSucceedsWithAllRequiredParameters(): void {
		$request = new EndpointRequest(userId: 1, params: ['slug' => 'one']);
		$response = $this->endpoint->run($request);

		$this->assertInstanceOf(EndpointResponse::class, $response);
		$this->assertEquals(200, $response->statusCode);
	}
}
