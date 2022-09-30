<?php

namespace Smolblog\Core;

use Smolblog\Core\Events\Startup;
use Smolblog\Core\Connector\ConnectionCredentialFactory;
use Smolblog\Core\Transient\TransientFactory;
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

	public function testItCanBeStarted(): void {
		$this->app->container->addShared(ModelHelper::class, fn() => $this->createStub(ModelHelper::class));

		$this->app->container->extend(ConnectionCredentialFactory::class)->addArgument(ModelHelper::class);
		$this->app->container->extend(TransientFactory::class)->addArgument(ModelHelper::class);

		$callbackHit = false;
		$this->app->events->subscribeTo(
			Startup::class,
			function($event) use (&$callbackHit) {
				$this->assertInstanceOf(Startup::class, $event);
				$callbackHit = true;
			}
		);

		$this->app->startup();
		$this->assertTrue($callbackHit);
	}
}
