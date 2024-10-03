<?php

namespace Smolblog\Test;

use PHPUnit\Framework\MockObject\MockObject;
use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Framework\Messages\Event;
use Smolblog\Test\Constraints\DomainEventChecker;
use Smolblog\Test\Kits\EventComparisonTestKit;

class ModelTest extends TestCase {
	use EventComparisonTestKit;

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
		// $this->mockEventBus->expects($this->once())->method('dispatch')->with($this->eventEquivalentTo($event));
		$this->expectEvents([$event]);
	}

	protected function expectEvents(array $events) {
		$this->mockEventBus->
			expects($this->exactly(count($events)))->
			method('dispatch')->
			with(new DomainEventChecker($events));
	}

	protected function expectNoEvents() {
		$this->mockEventBus->expects($this->never())->method('dispatch');
	}
}
