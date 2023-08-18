<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Test\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Psr\Log\NullLogger;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;

final class DefaultMessageBusTest extends TestCase {
	public function testItCallsListenersInOrder() {
		$providerStub = $this->createStub(ListenerProviderInterface::class);
		$providerStub->method('getListenersForEvent')->willReturn([
			fn($event) => $event->trace[] = 'first',
			fn($event) => $event->trace[] = 'second',
			fn($event) => $event->trace[] = 'third',
		]);

		$bus = new DefaultMessageBus($providerStub, new NullLogger());

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

		$bus = new DefaultMessageBus($providerStub, new NullLogger());

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
			fn($event) => $event->setResults($expected),
		]);

		$bus = new DefaultMessageBus($providerStub, new NullLogger());

		$message = new class() extends Query {};
		$actual = $bus->fetch($message);

		$this->assertEquals($expected, $actual);
	}

	public function testItCanWrapAMessageInAnAsyncMessageWrapper() {
		$message = new class($this->randomId()) extends Command { public function __construct(public readonly Identifier $thing) {} };
		$asyncMessage = new AsyncWrappedMessage($message);

		$providerStub = $this->createStub(ListenerProviderInterface::class);
		$providerStub->method('getListenersForEvent')->willReturn([
			fn($event) => $this->assertEquals($asyncMessage, $event),
		]);

		$bus = new DefaultMessageBus($providerStub, new NullLogger());

		$bus->dispatchAsync($message);
	}
}
