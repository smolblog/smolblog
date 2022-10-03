<?php

namespace Smolblog\Core\Connector;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Command\CommandBus;
use Smolblog\Core\Endpoint\{EndpointRequest, EndpointResponse};
use Smolblog\Test\EndpointTestToolkit;

final class ConnectCallbackTest extends TestCase {
	use EndpointTestToolkit;

	public function setUp(): void {
		$connectors = $this->createStub(ConnectorRegistrar::class);
		$connectors->method('has')->willReturn(true);

		$stateRepo = $this->createStub(AuthRequestStateReader::class);
		$stateRepo->method('has')->willReturn(true);

		$commands = $this->createStub(CommandBus::class);

		$this->endpoint = new ConnectCallback(
			connectors: $connectors,
			stateRepo: $stateRepo,
			commands: $commands,
		);
	}

	public function testItSucceedsWithAllRequiredParameters(): void {
		$request = new EndpointRequest(params: ['slug' => 'one', 'state' => 'two', 'code' => 'three']);
		$response = $this->endpoint->run($request);

		$this->assertInstanceOf(EndpointResponse::class, $response);
		$this->assertEquals(200, $response->statusCode);
	}
}
