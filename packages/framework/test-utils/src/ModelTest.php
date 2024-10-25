<?php

namespace Smolblog\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Test\Constraints\DomainEventChecker;

class ModelTest extends TestCase {
	const INCLUDED_MODELS = [];

	protected EventDispatcherInterface & MockObject $mockEventBus;
	protected TestApp $app;

	protected function setUp(): void {
		$this->mockEventBus = $this->createMock(EventDispatcherInterface::class);

		$mockServices = [
			EventDispatcherInterface::class => fn() => $this->mockEventBus,
			...$this->createMockServices(),
		];

		$this->app = new TestApp(models: static::INCLUDED_MODELS, services: $mockServices);
	}

	protected function createMockServices(): array {
		return [];
	}

	protected function expectEvent(DomainEvent $event) {
		$this->expectEvents([$event]);
	}

	protected function expectEvents(array $events, bool $checkProcess = false) {
		$this->mockEventBus->
			expects($this->exactly(count($events)))->
			method('dispatch')->
			with(new DomainEventChecker($events, $checkProcess));
	}

	protected function expectNoEvents() {
		$this->mockEventBus->expects($this->never())->method('dispatch');
	}
}
