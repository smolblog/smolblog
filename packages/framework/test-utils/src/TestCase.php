<?php

namespace Smolblog\Test;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\RandomIdentifier;

class TestCase extends PHPUnitTestCase {
	protected mixed $subject;

	protected function randomId(bool $scrub = false): Identifier {
		$id = new RandomIdentifier();

		return $scrub ? $this->scrubId($id) : $id;
	}

	protected function scrubId(Identifier $id): Identifier {
		return Identifier::fromByteString($id->toByteString());
	}

	// TODO: move this to a trait.
	protected function mockValue(string $class, mixed ...$overrides): mixed {
		$params = (new \ReflectionClass($class))->getConstructor()->getParameters();
		$props = [];
		foreach($params as $param) {
			$name = $param->getName();
			if (isset($overrides[$name])) {
				$props[$name] = $overrides['name'];
				continue;
			}

			$props[$name] = match ($param->getType()->__toString()) {
				'array' => [],
				'int' => 42,
				'float' => 3.14,
				'string' => 'smol',
				Identifier::class => new RandomIdentifier(),
				DateTimeField::class => new DateTimeField(),
				default => 'try',
			};
		}

		return new $class(...$props);
	}
}
