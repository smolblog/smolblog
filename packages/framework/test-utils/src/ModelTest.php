<?php

namespace Smolblog\Test;

use Crell\Tukio\Dispatcher;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Test\Constraints\DomainEventChecker;

class ModelTest extends AppTest {
	const INCLUDED_MODELS = [];

	protected EventDispatcherInterface & MockObject $mockEventBus;

	protected function setUp(): void {
		$this->mockEventBus = $this->createMock(EventDispatcherInterface::class);

		parent::setUp();
	}

	protected function createMockServices(): array {
		return [EventDispatcherInterface::class => fn() => $this->mockEventBus];
	}

	protected function expectEvent(DomainEvent $event) {
		$this->expectEvents([$event]);
	}

	protected function expectEvents(array $events, bool $checkProcess = false) {
		$this->mockEventBus->
			expects($this->exactly(count($events)))->
			method('dispatch')->
			with(new DomainEventChecker($events, $checkProcess))->
			willReturnCallback(fn($event) => $this->app->container->get(Dispatcher::class)->dispatch($event));
	}

	protected function expectNoEvents() {
		$this->mockEventBus->expects($this->never())->method('dispatch');
	}

	protected function expectEventOfType(string $type) {
		$this->mockEventBus->expects($this->once())->method('dispatch')->with($this->isInstanceOf($type));
	}
}
