<?php

namespace Smolblog\Core;

use \JsonSerializable;
use PHPUnit\Framework\TestCase;
use Smolblog\Core\Definitions\HttpVerb;
use Smolblog\Core\Definitions\SecurityLevel;

final class EndpointTestImplemented extends Endpoint {
	public function run(EndpointRequest $request): EndpointResponse {
		return new EndpointResponse( [ 'test' => 'pass' ] );
	}
}

final class EndpointTest extends TestCase {
	public function testItsRouteIsItsFullyQualifiedClassName(): void {
		$endpoint = new EndpointTestImplemented();
		$this->assertEquals(
			'smolblog/core/endpointtestimplemented',
			$endpoint->route
		);
	}

	public function testItRespondsToGet(): void {
		$endpoint = new EndpointTestImplemented();
		$this->assertEquals(
			[ HttpVerb::GET ],
			$endpoint->verbs
		);
	}

	public function testItIsPublicallyAccessible(): void {
		$endpoint = new EndpointTestImplemented();
		$this->assertEquals(
			SecurityLevel::Anonymous,
			$endpoint->security
		);
	}

	public function testItHasNoParameters(): void {
		$endpoint = new EndpointTestImplemented();
		$this->assertEmpty($endpoint->params);
	}

	public function testItSuccessfullyResponds(): void {
		$endpoint = new EndpointTestImplemented();
		$response = $endpoint->run(new EndpointRequest());

		$this->assertEquals(200, $response->statusCode);
		$this->assertEquals(
			'{"test":"pass"}',
			json_encode($response->body)
		);
	}
}
