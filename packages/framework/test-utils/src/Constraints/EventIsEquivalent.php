<?php

namespace Smolblog\Test\Constraints;

use InvalidArgumentException;
use PHPUnit\Framework\Constraint\Constraint;
use SebastianBergmann\Comparator\ComparisonFailure;
use Smolblog\Framework\Messages\Event;

class EventIsEquivalent extends Constraint {
	public function __construct(private Event $expected) {}

	public function toString(): string { return 'two Events are equivalent'; }
	protected function failureDescription($other): string { return $this->toString(); }

	protected function matches(mixed $other): bool {
		if (!is_a($other, Event::class)) {
			throw new InvalidArgumentException('Object is not an Event.');
		}

		$expectedData = $this->expected->toArray();
		unset($expectedData['id']);
		unset($expectedData['timestamp']);

		$actualData = $other->toArray();
		unset($actualData['id']);
		unset($actualData['timestamp']);

		return $expectedData == $actualData;
	}

	protected function fail(mixed $other, string $description, ?ComparisonFailure $comparisonFailure = null): never
	{
		if ($comparisonFailure === null) {
			$expectedData = $this->expected->toArray();
			unset($expectedData['id']);
			unset($expectedData['timestamp']);

			$actualData = $other->toArray();
			unset($actualData['id']);
			unset($actualData['timestamp']);

			$comparisonFailure = new ComparisonFailure(
				$this->expected,
				$other,
				$this->exporter()->export($expectedData),
				$this->exporter()->export($actualData),
				'Failed asserting that two Events are equivalent.'
			);
		}

		parent::fail($other, $description, $comparisonFailure);
	}
}
