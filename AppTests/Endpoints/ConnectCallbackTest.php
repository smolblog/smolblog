<?php

namespace Smolblog\App\Endpoints;

use PHPUnit\Framework\TestCase;
use Smolblog\App\Endpoint\{EndpointRequest, EndpointResponse};
use Smolblog\Core\Connector\Entities\AuthRequestStateReader;
use Smolblog\Core\Connector\ConnectorRegistrar;
use Smolblog\Core\Connector\Entities\AuthRequestState;
use Smolblog\Framework\Executor;
use Smolblog\Test\EndpointTestToolkit;

final class ConnectCallbackTest extends TestCase {
	use EndpointTestToolkit;

	public function setUp(): void {
		$connectors = $this->createStub(ConnectorRegistrar::class);
		$connectors->method('has')->willReturnCallback(fn($slug) => $slug !== 'nope');

		$stateRepo = $this->createStub(AuthRequestStateReader::class);
		$stateRepo->method('has')->willReturnCallback(fn($id) => strval($id) !== strval(AuthRequestState::buildId(key: 'nope')));

		$commands = $this->createStub(Executor::class);

		$this->endpoint = new ConnectCallback(
			connectors: $connectors,
			stateRepo: $stateRepo,
			commands: $commands,
		);
	}

	public function testItSendsBadRequestWhenSlugIsMissing(): void {
		$request = new EndpointRequest(params: ['state' => 'two', 'code' => 'three']);
		$response = $this->endpoint->run($request);
		$this->assertEquals(400, $response->statusCode);
	}

	public function testItSendsNotFoundWhenSlugIsNotRegistered(): void {
		$request = new EndpointRequest(params: ['slug' => 'nope', 'state' => 'two', 'code' => 'three']);
		$response = $this->endpoint->run($request);
		$this->assertEquals(404, $response->statusCode);
	}

	public function testItSendsBadRequestWhenStateIsMissing(): void {
		$request = new EndpointRequest(params: ['state' => 'two', 'code' => 'three']);
		$response = $this->endpoint->run($request);
		$this->assertEquals(400, $response->statusCode);
	}

	public function testItSendsBadRequestWhenStateIsNotRegistered(): void {
		$request = new EndpointRequest(params: ['slug' => 'one', 'state' => 'nope', 'code' => 'three']);
		$response = $this->endpoint->run($request);
		$this->assertEquals(400, $response->statusCode);
	}

	public function testItSendsBadRequestWhenCodeIsMissing(): void {
		$request = new EndpointRequest(params: ['state' => 'two', 'state' => 'three']);
		$response = $this->endpoint->run($request);
		$this->assertEquals(400, $response->statusCode);
	}

	public function testItSucceedsWithAllRequiredParameters(): void {
		$request = new EndpointRequest(params: ['slug' => 'one', 'state' => 'two', 'code' => 'three']);
		$response = $this->endpoint->run($request);

		$this->assertInstanceOf(EndpointResponse::class, $response);
		$this->assertEquals(200, $response->statusCode);
	}

	public function testItSucceedsWithOauth1Parameters(): void {
		$request = new EndpointRequest(params: ['slug' => 'one', 'oauth_token' => 'two', 'oauth_verifier' => 'three']);
		$response = $this->endpoint->run($request);

		$this->assertInstanceOf(EndpointResponse::class, $response);
		$this->assertEquals(200, $response->statusCode);
	}
}
