<?php

namespace Smolblog\Core;

use Smolblog\Core\Events\Startup;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase {
	public function testItCanBeInstantiated(): void {
		$endpointDouble = $this->createStub(EndpointRegistrar::class);
		$environment = new Environment(apiBase: 'https://smol.blog/api/');

		$app = new App(
			withEndpointRegistrar: $endpointDouble,
			withEnvironment: $environment
		);

		$this->assertInstanceOf(Container::class, $app->container);
	}
}
