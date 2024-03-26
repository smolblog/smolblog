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
abstract readonly class Value implements SerializableValue {
	use SerializableValueKit;

	/**
	 * Create a copy of the object with the given properties replacing existing ones.
	 *
	 * @throws InvalidValueProperties When the object cannot be copied.
	 *
	 * @param mixed ...$props Properties to override.
	 * @return static
	 */
	public function with(mixed ...$props): static {
		$base = array_intersect_key(get_object_vars($this), static::propertyInfo());

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
