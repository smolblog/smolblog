<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Test\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Smolblog\Framework\Messages\Query;

final class DefaultMessageBusTest extends TestCase {
	public function testItCallsListenersInOrder() {
		$providerStub = $this->createStub(ListenerProviderInterface::class);
		$providerStub->method('getListenersForEvent')->willReturn([
			fn($event) => $event->trace[] = 'first',
			fn($event) => $event->trace[] = 'second',
			fn($event) => $event->trace[] = 'third',
		]);

		$bus = new DefaultMessageBus($providerStub);

		$message = new class() { public $trace = []; };
		$bus->dispatch($message);

		$this->assertEquals(['first','second','third'], $message->trace);
	}

	public function testItStopsCallingListenersWhenEventStopped() {
		$providerStub = $this->createStub(ListenerProviderInterface::class);
		$providerStub->method('getListenersForEvent')->willReturn([
			fn($event) => $event->trace[] = 'first',
			fn($event) => $event->active = false,
			fn($event) => $event->trace[] = 'third',
		]);

		$bus = new DefaultMessageBus($providerStub);

		$message = new class() implements StoppableEventInterface {
			public $trace = [];
			public $active = true;
			public function isPropagationStopped(): bool { return !$this->active; }
		};
		$bus->dispatch($message);

		$this->assertEquals(['first'], $message->trace);
	}

	public function testItUnwrapsAQueryWhenFetched() {
		$expected = '59cb2796-411c-4f3d-89f2-07dae78787e6';

		$providerStub = $this->createStub(ListenerProviderInterface::class);
		$providerStub->method('getListenersForEvent')->willReturn([
			fn($event) => $event->results = $expected,
		]);

		$bus = new DefaultMessageBus($providerStub);

		$message = new class() extends Query {};
		$actual = $bus->fetch($message);

		$this->assertEquals($expected, $actual);
	}
}
