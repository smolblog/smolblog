<?php

namespace Smolblog\Core;

use Smolblog\Core\Events\Startup;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase {
	public function testItSendsEventOnStartup(): void {
		$containerDouble = $this->createStub(Container::class);
		$endpointDouble = $this->createStub(EndpointRegistrar::class);
		$dispatcherDouble = $this->createMock(EventDispatcher::class);

		$dispatcherDouble->expects($this->once())
										 ->method('dispatch')
										 ->with($this->callback(function($param) {
											 return $param instanceof Startup && $param->app instanceof App;
										 }));

		$app = new App(
			withContainer: $containerDouble,
			withDispatcher: $dispatcherDouble,
			withEndpointRegistrar: $endpointDouble
		);
		$app->startup();
	}

	public function testItRegistersAllClassesOnStartup(): void {
		$containerDouble = $this->createMock(Container::class);
		$endpointDouble = $this->createStub(EndpointRegistrar::class);
		$dispatcherDouble = $this->createStub(EventDispatcher::class);

		$containerDouble->expects($this->exactly(4))
			->method('add')
			->withConsecutive(
				[$this->equalTo(Models\ConnectionCredential::class)],
				[$this->equalTo(Models\User::class)],
				[$this->equalTo(Endpoints\ConnectCallback::class)],
				[$this->equalTo(Endpoints\ConnectInit::class)]
			);

		$app = new App(
			withContainer: $containerDouble,
			withDispatcher: $dispatcherDouble,
			withEndpointRegistrar: $endpointDouble
		);
		$app->startup();
	}
}
