<?php

namespace Smolblog\Infrastructure\Registries;

use Psr\EventDispatcher\EventDispatcherInterface;
use Smolblog\Foundation\Service\Event\EventListener;
use Smolblog\Foundation\Service\Event\EventListenerService;
use Smolblog\Infrastructure\Model;
use Smolblog\Test\AppTest;
use Smolblog\Test\TestCase;

class EventTracer {
	public array $trace = [];
}

class EventOne extends EventTracer {
	public function __construct(public string $word, public int $num) {}
}

final class EventTwo extends EventTracer {
	public function __construct(public string $word, public int $num) {}
}

final class EventThree extends EventOne {}

final class EventFour extends EventOne {}

final class EventListenerOne implements EventListenerService {
	#[EventListener]
	public function doEventOne(EventOne $cmd) { $cmd->trace[] = self::class . '::doEventOne'; }
	#[EventListener]
	public function doEventTwo(EventTwo $cmd) { $cmd->trace[] = self::class . '::doEventTwo'; }
}

final class EventListenerTwo implements EventListenerService {
	#[EventListener(earlier: 1)]
	public function doEventThree(EventThree $cmd) { $cmd->trace[] = self::class . '::doEventThree'; }
	#[EventListener(later: 1)]
	public function doEventFour(EventFour $cmd) { $cmd->trace[] = self::class . '::doEventFour'; }
}

final class EventListenerRegistryTest extends AppTest {
	const INCLUDED_MODELS = [Model::class];

	protected function createMockServices(): array
	{
		return [
			EventListenerOne::class => [],
			EventListenerTwo::class => [],
		];
	}

	public function testItDispatchesAnEventToItsListener() {
		$service = $this->app->container->get(EventDispatcherInterface::class);
		$event = new EventTwo(word: 'up', num: 34);

		$service->dispatch($event);
		$this->assertEquals([EventListenerOne::class . '::doEventTwo'], $event->trace);
	}

	public function testListenersAreGivenSubclassesOfListenedEvents() {
		$service = $this->app->container->get(EventDispatcherInterface::class);
		$event = new EventFour(word: 'up', num: 34);

		$service->dispatch($event);
		$this->assertEquals([
			EventListenerOne::class . '::doEventOne',
			EventListenerTwo::class . '::doEventFour',
		], $event->trace);
	}

	public function testListenersAreCalledInGivenOrder() {
		$service = $this->app->container->get(EventDispatcherInterface::class);
		$event = new EventThree(word: 'up', num: 34);

		$service->dispatch($event);
		$this->assertEquals([
			EventListenerTwo::class . '::doEventThree',
			EventListenerOne::class . '::doEventOne',
		], $event->trace);
	}
}
