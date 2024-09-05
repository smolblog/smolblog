<?php

namespace Smolblog\Test\Constraints;

use InvalidArgumentException;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Util\Exporter;
use SebastianBergmann\Comparator\ComparisonFailure;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Framework\Messages\Event;

class EventIsEquivalent extends Constraint {
	public function __construct(private Event|DomainEvent $expected) {}

	public function toString(): string { return 'two Events are equivalent'; }
	protected function failureDescription($other): string { return $this->toString(); }

	protected function matches(mixed $other): bool {
		if (!(is_a($other, Event::class) || is_a($other, DomainEvent::class))) {
			throw new InvalidArgumentException('Object is not an Event.');
		}

		$expectedData = $this->eventToArray($this->expected);
		unset($expectedData['id']);
		unset($expectedData['timestamp']);

		$actualData = $this->eventToArray($other);
		unset($actualData['id']);
		unset($actualData['timestamp']);

		return $expectedData == $actualData;
	}

	protected function fail(mixed $other, string $description, ?ComparisonFailure $comparisonFailure = null): never
	{
		if ($comparisonFailure === null) {
			$expectedData = $this->eventToArray($this->expected);
			unset($expectedData['id']);
			unset($expectedData['timestamp']);

			$actualData = $this->eventToArray($other);
			unset($actualData['id']);
			unset($actualData['timestamp']);

			$comparisonFailure = new ComparisonFailure(
				$this->expected,
				$other,
				Exporter::export($expectedData),
				Exporter::export($actualData),
				'Failed asserting that two Events are equivalent.'
			);
		}

		parent::fail($other, $description, $comparisonFailure);
	}

	private function eventToArray(Event|DomainEvent $event) {
		if (is_a($event, Event::class)) {
			return $event->toArray();
		}

		return $event->serializeValue();
	}
}
