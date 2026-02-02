<?php

namespace Smolblog\Core\Media\Services;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerInterface;
use Cavatappi\Foundation\Exceptions\ServiceNotRegistered;
use Cavatappi\Test\TestCase;

abstract class MediaHandlerRegistryTestHandlerOne implements MediaHandler {
	public static function getKey(): string {
		return 'one';
	}
}
abstract class MediaHandlerRegistryTestHandlerTwo implements MediaHandler {
	public static function getKey(): string {
		return 'two';
	}
}

#[AllowMockObjectsWithoutExpectations]
final class MediaHandlerRegistryTest extends TestCase {
	private MediaHandler&MockObject $mockHandlerOne;
	private MediaHandler&MockObject $mockHandlerTwo;
	private ContainerInterface&MockObject $container;

	protected function setUp(): void {
		$this->mockHandlerOne = $this->createMock(MediaHandlerRegistryTestHandlerOne::class);
		$this->mockHandlerTwo = $this->createMock(MediaHandlerRegistryTestHandlerTwo::class);
		$this->container = $this->createMock(ContainerInterface::class);
	}

	public function testADefaultHandlerCanBeConfigured() {
		$registry = new MediaHandlerRegistry(container: $this->container, defaultHandlerKey: 'two');
		$registry->configure([
			MediaHandlerRegistryTestHandlerOne::class,
			MediaHandlerRegistryTestHandlerTwo::class,
		]);

		$this->container->method('has')->willReturn(true);
		$this->container->expects($this->once())
			->method('get')
			->with(MediaHandlerRegistryTestHandlerTwo::class)
			->willReturn($this->mockHandlerTwo);

		$this->assertEquals($this->mockHandlerTwo, $registry->get());
	}

	public function testFirstHandlerWillBeDefault() {
		$registry = new MediaHandlerRegistry(container: $this->container);
		$registry->configure([
			MediaHandlerRegistryTestHandlerOne::class,
			MediaHandlerRegistryTestHandlerTwo::class,
		]);

		$this->container->method('has')->willReturn(true);
		$this->container->expects($this->once())
			->method('get')
			->with(MediaHandlerRegistryTestHandlerOne::class)
			->willReturn($this->mockHandlerOne);

		$this->assertEquals($this->mockHandlerOne, $registry->get());
	}

	public function testItFailsIfNoHandlersRegistered() {
		$registry = new MediaHandlerRegistry(container: $this->container);

		$this->container->method('has')->willReturn(true);
		$this->container->expects($this->never())->method('get');

		$this->expectException(ServiceNotRegistered::class);

		$registry->get();
	}

	public function testAnyHandlerCanBeRetrieved() {
		$registry = new MediaHandlerRegistry(container: $this->container, defaultHandlerKey: 'two');
		$registry->configure([
			MediaHandlerRegistryTestHandlerOne::class,
			MediaHandlerRegistryTestHandlerTwo::class,
		]);

		$this->container->method('has')->willReturn(true);
		$this->container->expects($this->once())
			->method('get')
			->with(MediaHandlerRegistryTestHandlerOne::class)
			->willReturn($this->mockHandlerOne);

		$this->assertTrue($this->mockHandlerOne === $registry->get('one'));
	}
}
