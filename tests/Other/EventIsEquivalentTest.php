<?php

namespace Smolblog\Test;

use InvalidArgumentException;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Test\TestCase;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Test\Kits\EventComparisonTestKit;

final readonly class TestEvent extends DomainEvent {
	public function __construct(public readonly string $property) {
		parent::__construct(userId: Identifier::fromString('47adfd5b-4f96-4eb1-9f93-1b30ee0d17d2'));
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
