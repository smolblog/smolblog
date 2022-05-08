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
			public function params(): array { return []; }
			public function json(): array|false { return false; }
			public function environment(): array { return []; }
		});

		$this->assertEquals(200, $response->statusCode());
		$this->assertEquals(
			'{"test":"pass"}',
			json_encode($response->body())
		);
	}
}
