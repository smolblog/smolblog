<?php

namespace Smolblog\Core;

use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase {
	private $app;
	public function setUp(): void {
		$endpointDouble = $this->createStub(Endpoint\EndpointRegistrar::class);
		$environment = new Environment(apiBase: 'https://smol.blog/api/');

		$this->app = new App(
			withEndpointRegistrar: $endpointDouble,
			withEnvironment: $environment
		);
	}

	public function testItCanBeInstantiated(): void {
		$this->assertInstanceOf(App::class, $this->app);
	}
}
