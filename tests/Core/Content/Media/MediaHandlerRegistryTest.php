<?php

namespace Smolblog\Core\ContentV1\Media;

use Psr\Container\ContainerInterface;
use Smolblog\Test\TestCase;

abstract class MediaHandlerRegistryTestHandlerOne implements MediaHandler {
	public static function getHandle(): string { return 'handlerOne'; }
}

abstract class MediaHandlerRegistryTestHandlerTwo implements MediaHandler {
	public static function getHandle(): string { return 'handlerTwo'; }
}

final class MediaHandlerRegistryTest extends TestCase {
	private function setUpController(): ContainerInterface {
		$handlerOne = $this->createStub(MediaHandlerRegistryTestHandlerOne::class);
		$handlerTwo = $this->createStub(MediaHandlerRegistryTestHandlerTwo::class);

		$container = $this->createStub(ContainerInterface::class);
		$container->method('has')->willReturn(true);
		$container->method('get')->willReturnCallback(
			fn($key) => $key === MediaHandlerRegistryTestHandlerOne::class ? $handlerOne : $handlerTwo
		);

		return $container;
	}

	public function testItRegistersMediaHandlers() {
		$this->assertEquals(MediaHandler::class, MediaHandlerRegistry::getInterfaceToRegister());
	}

	public function testItWillRetrieveASpecificHandlerByKey() {
		$container = $this->setUpController();
		$registry = new MediaHandlerRegistry(container: $container, configuration: [
			MediaHandlerRegistryTestHandlerOne::class,
			MediaHandlerRegistryTestHandlerTwo::class,
		]);

		$this->assertInstanceOf(MediaHandlerRegistryTestHandlerOne::class, $registry->get('handlerOne'));
		$this->assertInstanceOf(MediaHandlerRegistryTestHandlerTwo::class, $registry->get('handlerTwo'));
	}

	public function testItWillRetrieveADefaultHandlerWhenNoKeyIsGiven() {
		$container = $this->setUpController();
		$registry = new MediaHandlerRegistry(container: $container, configuration: [
			MediaHandlerRegistryTestHandlerOne::class,
		]);

		$this->assertInstanceOf(MediaHandlerRegistryTestHandlerOne::class, $registry->get());
	}
}
