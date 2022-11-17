<?php

namespace Smolblog\Core\Connector;

use PHPUnit\Framework\TestCase;
use Smolblog\App\Environment;
use Smolblog\Core\Command\CommandBus;
use Smolblog\Core\Endpoint\{EndpointRequest, EndpointResponse};
use Smolblog\Test\EndpointTestToolkit;

final class ConnectInitTest extends TestCase {
	use EndpointTestToolkit;

	public function setUp(): void {
		$connectors = $this->createStub(ConnectorRegistrar::class);
		$connectors->method('has')->willReturnCallback(fn($slug) => $slug !== 'nope');

		$commands = $this->createStub(CommandBus::class);
		$commands->method('handle')->willReturn('//smol.blog');

		$this->endpoint = new ConnectInit(
			env: new Environment(apiBase: '//smol.blog'),
			connectors: $connectors,
			commands: $commands,
		);
	}

	public function testItSendsBadRequestWhenSlugIsMissing(): void {
		$response = $this->endpoint->run(new EndpointRequest(userId: 1));
		$this->assertEquals(400, $response->statusCode);
	}

	public function testItSendsBadRequestWhenUnauthenticated(): void {
		$response = $this->endpoint->run(new EndpointRequest(params: ['slug' => 'one']));
		$this->assertEquals(400, $response->statusCode);
	}

	public function testItSendsNotFoundWhenSlugIsNotRegistered(): void {
		$response = $this->endpoint->run(new EndpointRequest(userId: 1, params: ['slug' => 'nope']));
		$this->assertEquals(404, $response->statusCode);
	}

	public function testItSucceedsWithAllRequiredParameters(): void {
		$request = new EndpointRequest(userId: 1, params: ['slug' => 'one']);
		$response = $this->endpoint->run($request);

		$this->assertInstanceOf(EndpointResponse::class, $response);
		$this->assertEquals(200, $response->statusCode);
	}
}
