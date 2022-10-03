<?php

namespace Smolblog\Core\Connector;

use PHPUnit\Framework\TestCase;
use Smolblog\Core\Environment;
use Smolblog\Core\Command\CommandBus;
use Smolblog\Core\Endpoint\{EndpointRequest, EndpointResponse};
use Smolblog\Test\EndpointTestToolkit;

final class ConnectInitTest extends TestCase {
	use EndpointTestToolkit;

	public function setUp(): void {
		$connectors = $this->createStub(ConnectorRegistrar::class);
		$connectors->method('has')->willReturn(true);

		$commands = $this->createStub(CommandBus::class);
		$commands->method('handle')->willReturn('//smol.blog');

		$this->endpoint = new ConnectInit(
			env: new Environment(apiBase: '//smol.blog'),
			connectors: $connectors,
			commands: $commands,
		);
	}

	public function testItSucceedsWithAllRequiredParameters(): void {
		$request = new EndpointRequest(userId: 1, params: ['slug' => 'one']);
		$response = $this->endpoint->run($request);

		$this->assertInstanceOf(EndpointResponse::class, $response);
		$this->assertEquals(200, $response->statusCode);
	}
}
