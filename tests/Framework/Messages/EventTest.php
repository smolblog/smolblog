<?php

namespace Smolblog\Framework\Messages;

use DateTimeImmutable;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\StoppableEventInterface;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Test\DateIdentifierTestKit;

final class EventTest extends TestCase {
	use DateIdentifierTestKit;

	public function testTheDefaultTimestampIsNow() {
		$event = new class('camelot') extends Event {
			public function __construct(
				public readonly string $payload
			) {
				parent::__construct();
			}
		};

		$this->assertEquals('camelot', $event->payload);
		$this->assertIdentifiersHaveSameDate(Identifier::createFromDate($event->timestamp), $event->id);
	}

	public function testTheDefaultIdentifierIsCreatedFromTheTimestamp() {
		$testTime = new DateTimeImmutable('2019-01-06T20:04:31.0Z');
		$event = new class('camelot', $testTime) extends Event {
			public function __construct(
				public readonly string $payload,
				DateTimeInterface $timestamp,
			) {
				parent::__construct(timestamp: $timestamp);
			}
		};

		$this->assertEquals('camelot', $event->payload);
		$this->assertEquals($testTime->format(DateTimeInterface::COOKIE), $event->timestamp->format(DateTimeInterface::COOKIE));
		$this->assertIdentifiersHaveSameDate(Identifier::createFromDate($testTime), $event->id);
	}

	public function testItCanBeStoppable() {
		$event = new class('camelot') extends Event {
			use StoppableMessageKit;
			public function __construct(
				public readonly string $payload
			) {
				parent::__construct();
			}
		};

		$this->assertEquals('camelot', $event->payload);
		$this->assertFalse($event->isPropagationStopped());

		$event->stopMessage();

		$this->assertTrue($event->isPropagationStopped());
	}
}
