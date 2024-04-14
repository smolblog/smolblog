<?php

namespace Smolblog\Test;

use InvalidArgumentException;
use Smolblog\Test\TestCase;
use Smolblog\Foundation\Value\Fields\RandomIdentifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Test\Kits\EventComparisonTestKit;

final readonly class TestEvent extends DomainEvent {
	public function __construct(public readonly string $property) {
		parent::__construct(userId: new RandomIdentifier());
	}
}

final class EventIsEquivalentTest extends TestCase {
	use EventComparisonTestKit;

	public function testEquivalentEventsPass() {
		$eventOne = new TestEvent(property: 'Hello!');
		$eventTwo = new TestEvent(property: 'Hello!');

		$this->assertThat($eventTwo, $this->eventEquivalentTo($eventOne));
	}

	public function testDifferentEventsFail() {
		$eventOne = new TestEvent(property: 'Hello!');
		$eventTwo = new TestEvent(property: 'Goodbye!');

		$this->assertThat($eventTwo, $this->logicalNot($this->eventEquivalentTo($eventOne)));
	}

	public function testNotPassingAnEventWillThrowException() {
		$this->expectException(InvalidArgumentException::class);

		$eventOne = new TestEvent(property: 'Hello!');
		$eventTwo = 'Hello!';

		$this->assertThat($eventTwo, $this->eventEquivalentTo($eventOne));
	}
}
