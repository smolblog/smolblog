<?php

namespace Smolblog\Core\Endpoints;

use \JsonSerializable;
use PHPUnit\Framework\TestCase;
use Smolblog\Core\Definitions\EndpointRequest;
use Smolblog\Core\Definitions\HttpVerb;
use Smolblog\Core\Definitions\SecurityLevel;
use Smolblog\Core\Endpoints\BasicPublicEndpoint;

final class ConcreteBasicPublicEndpoint extends BasicPublicEndpoint {
	protected function responseBody(): array|JsonSerializable {
		return [ 'test' => 'pass' ];
	}
}

final class BasicPublicEndpointTest extends TestCase {
	public function testItsRouteIsItsFullyQualifiedClassName(): void {
		$endpoint = new ConcreteBasicPublicEndpoint();
		$this->assertEquals(
			'smolblog/core/endpoints/concretebasicpublicendpoint',
			$endpoint->route()
		);
	}

	public function testItRespondsToGet(): void {
		$endpoint = new ConcreteBasicPublicEndpoint();
		$this->assertEquals(
			[ HttpVerb::GET ],
			$endpoint->verbs()
		);
	}

	public function testItIsPublicallyAccessible(): void {
		$endpoint = new ConcreteBasicPublicEndpoint();
		$this->assertEquals(
			SecurityLevel::Anonymous,
			$endpoint->security()
		);
	}

	public function testItHasNoParameters(): void {
		$endpoint = new ConcreteBasicPublicEndpoint();
		$this->assertEmpty($endpoint->params());
	}

	public function testItSuccessfullyResponds(): void {
		$endpoint = new ConcreteBasicPublicEndpoint();
		$response = $endpoint->run(new class implements EndpointRequest {
			public function verb(): HttpVerb { return HttpVerb::GET; }
			public function url(): string { return ''; }
			public function headers(): array { return []; }
			public function body(): string { return ''; }
			public function params(): array { return []; }
			public function json(): array|false { return false; }
		});

		$this->assertEquals(200, $response->statusCode());
		$this->assertEmpty($response->headers());
		$this->assertEquals(
			'{"test":"pass"}',
			$response->body()
		);
	}
}
