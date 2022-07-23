<?php

namespace Smolblog\Core;

use Smolblog\Core\Events\Startup;
use PHPUnit\Framework\TestCase;

final class AppTest extends TestCase {
	public function testAppStartsSuccessfully(): void {
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
}
