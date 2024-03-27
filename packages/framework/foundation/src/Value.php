<?php

namespace Smolblog\Framework\Foundation;

use Smolblog\Framework\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Framework\Foundation\Value\Traits\{SerializableValue, SerializableValueKit};
use Throwable;

/**
 * Read-only data structure.
 *
 * Useful for passing data around from object to object. Value objects are intended to be immutable; there is a
 * metadata class available for attaching runtime data.
 *
 * Declaring `readonly` properties in a defined object allows PHP to typecheck the object instead of relying on arrays
 * with specific keys.
 */
abstract readonly class Value {
	/**
	 * Create a copy of the object with the given properties replacing existing ones.
	 *
	 * @throws InvalidValueProperties When the object cannot be copied.
	 *
	 * @param mixed ...$props Properties to override.
	 * @return static
	 */
	public function with(mixed ...$props): static {
		// Calling get_object_vars from outside context so that we only get public properties.
		// see https://stackoverflow.com/questions/13124072/
		$base = get_object_vars(...)->__invoke($this);

		try {
			return new static(...array_merge($base, $props));
		} catch (Throwable $e) {
			throw new InvalidValueProperties(
				message: 'Unable to copy Value in ' . static::class . '::with(): ' . $e->getMessage(),
				previous: $e,
			);
		}
	}
}
