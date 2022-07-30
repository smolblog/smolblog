<?php

namespace Smolblog\Core;

use Smolblog\Core\Events\Startup;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase {
	private $app;
	public function setUp(): void {
		$endpointDouble = $this->createStub(EndpointRegistrar::class);
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
