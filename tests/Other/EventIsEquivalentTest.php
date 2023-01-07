<?php

namespace Smolblog\Test;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Smolblog\Framework\Messages\Event;

final class TestEvent extends Event {
	public function __construct(public readonly string $property) { parent::__construct(); }
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