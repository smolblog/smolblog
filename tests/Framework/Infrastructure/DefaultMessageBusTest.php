<?php

namespace Smolblog\Framework\Infrastructure;

use Smolblog\Test\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

final class DefaultMessageBusTest extends TestCase {
	private ListenerProviderInterface $provider;

	protected function setUp(): void {
		$this->provider = $this->createMock(ListenerProviderInterface::class);

		$this->subject = new DefaultMessageBus(
			provider: $this->provider,
		);
	}

	public function testItCallsListenersInOrder() {
		$this->provider->method('getListenersForEvent')->willReturn([
			fn($event) => $event->trace[] = 'first',
			fn($event) => $event->trace[] = 'second',
			fn($event) => $event->trace[] = 'third',
		]);

		$message = new class() { public $trace = []; };
		$this->subject->dispatch($message);

		$this->assertEquals(['first','second','third'], $message->trace);
	}

	public function testItStopsCallingListenersWhenEventStopped() {
		$this->provider->method('getListenersForEvent')->willReturn([
			fn($event) => $event->trace[] = 'first',
			fn($event) => $event->active = false,
			fn($event) => $event->trace[] = 'third',
		]);

		$message = new class() implements StoppableEventInterface {
			public $trace = [];
			public $active = true;
			public function isPropagationStopped(): bool { return !$this->active; }
		};
		$this->subject->dispatch($message);

		$this->assertEquals(['first'], $message->trace);
	}

	public function testItUnwrapsAQueryWhenFetched() {
		$expected = '59cb2796-411c-4f3d-89f2-07dae78787e6';

		$this->provider->method('getListenersForEvent')->willReturn([
			fn($event) => $event->setResults($expected),
		]);

		$message = new readonly class() extends Query {};
		$actual = $this->subject->fetch($message);

		$this->assertEquals($expected, $actual);
	}

	public function testItCanWrapAMessageInAnAsyncMessageWrapper() {
		$message = new readonly class($this->randomId()) extends Command {
			public function __construct(public readonly Identifier $thing) {}
		};
		$asyncMessage = new AsyncWrappedMessage($message);

		$this->provider->method('getListenersForEvent')->willReturn([
			fn($event) => $this->assertEquals($asyncMessage, $event),
		]);

		$this->subject->dispatchAsync($message);
	}
}
