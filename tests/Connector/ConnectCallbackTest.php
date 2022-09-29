<?php

namespace Smolblog\Core\Connector;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Endpoint\{EndpointRequest, EndpointResponse};
use Smolblog\Core\Transient\TransientFactory;
use Smolblog\Test\EndpointTestToolkit;

final class ConnectCallbackTest extends TestCase {
	use EndpointTestToolkit;

	public function setUp(): void {
		$connector = $this->createStub(Connector::class);
		$connector->method('createCredential')->willReturn($this->createStub(ConnectionCredential::class));

		$connectors = $this->createStub(ConnectorRegistrar::class);
		$connectors->method('retrieve')->willReturn($connector);

		$transients = $this->createStub(TransientFactory::class);
		$transients->method('getTransient')->willReturn(['thing'=>'one']);

		$this->endpoint = new ConnectCallback(
			connectors: $connectors,
			transients: $transients,
		);
	}

	public function testItSucceedsWithAllRequiredParameters(): void {
		$request = new EndpointRequest(params: ['slug' => 'one', 'state' => 'two', 'code' => 'three']);
		$response = $this->endpoint->run($request);

		$this->assertInstanceOf(EndpointResponse::class, $response);
		$this->assertEquals(200, $response->statusCode);
	}
}
