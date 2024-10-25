<?php

namespace Smolblog\Test\Constraints;

use InvalidArgumentException;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Util\Exporter;
use SebastianBergmann\Comparator\ComparisonFailure;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

class DomainEventChecker extends Constraint {
	public function __construct(private array $expectedEvents, private bool $checkProcess = false) {}

	private ?DomainEvent $expected = null;
	private ?Identifier $processId = null;

	public function toString(): string { return 'two Events are equivalent'; }
	protected function failureDescription($other): string { return $this->toString(); }

	protected function matches(mixed $other): bool {
		$this->expected = \array_shift($this->expectedEvents);

		if (!is_a($other, DomainEvent::class)) {
			throw new InvalidArgumentException('Object is not an Event.');
		}

		$expectedData = $this->expected?->serializeValue() ?? [];
		unset($expectedData['id']);
		unset($expectedData['timestamp']);

		$actualData = $other->serializeValue();
		unset($actualData['id']);
		unset($actualData['timestamp']);

		if ($this->checkProcess) {
			$this->processId ??= $other->processId;
			$expectedData['processId'] = $this->processId?->toString() ?? '#ERR#';
		}

		return $expectedData == $actualData;
	}

	protected function fail(mixed $other, string $description, ?ComparisonFailure $comparisonFailure = null): never
	{
		if ($comparisonFailure === null) {
			$expectedData = $this->expected?->serializeValue() ?? [];
			unset($expectedData['id']);
			unset($expectedData['timestamp']);

			$actualData = $other->serializeValue();
			unset($actualData['id']);
			unset($actualData['timestamp']);

			if ($this->checkProcess) {
				$expectedData['processId'] = $this->processId?->toString() ?? '#ERR#';
			} else {
				unset($expectedData['processId'], $actualData['processId']);
			}

			$comparisonFailure = new ComparisonFailure(
				$this->expected,
				$other,
				Exporter::export($expectedData),
				Exporter::export($actualData),
				isset($this->expected) ? 'Failed asserting that two Events are equivalent.' : 'Event was not expected.'
			);
		}

		parent::fail($other, $description, $comparisonFailure);
	}
}
