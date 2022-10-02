<?php

namespace Smolblog\Core\Connector;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Endpoint\{EndpointRequest, EndpointResponse};
use Smolblog\Test\EndpointTestToolkit;

final class ConnectCallbackTest extends TestCase {
	use EndpointTestToolkit;

	public function setUp(): void {
		$connector = $this->createStub(Connector::class);
		$connector->method('createConnection')->willReturn(new Connection(
			userId: 5,
			provider: 'something',
			providerKey: 'something',
			displayName: 'something',
			details: ['something'=>'else'],
		));

		$connectors = $this->createStub(ConnectorRegistrar::class);
		$connectors->method('get')->willReturn($connector);

		$stateRepo = $this->createStub(ConnectionCreationStateRepository::class);
		$stateRepo->method('get')->willReturn(new ConnectionCreationState(
			id: 'two',
			userId: 5,
			info: ['six' => 'eight'],
		));

		$connectionRepo = $this->createStub(ConnectionRepository::class);

		$this->endpoint = new ConnectCallback(
			connectors: $connectors,
			stateRepo: $stateRepo,
			connectionRepo: $connectionRepo,
		);
	}

	public function testItSucceedsWithAllRequiredParameters(): void {
		$request = new EndpointRequest(params: ['slug' => 'one', 'state' => 'two', 'code' => 'three']);
		$response = $this->endpoint->run($request);

		$this->assertInstanceOf(EndpointResponse::class, $response);
		$this->assertEquals(200, $response->statusCode);
	}
}
