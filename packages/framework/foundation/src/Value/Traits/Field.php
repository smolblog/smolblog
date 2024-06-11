<?php

namespace Smolblog\Foundation\Value\Traits;

use Stringable;

/**
 * A value that can be serialized to a string.
 *
 * This is useful for values that are not entire objects but still need to conform to a pattern. For example, a UUID or
 * a date.
 */
interface Field extends Stringable, SerializableValue {
	/**
	 * Provide the serialized value as the string representation.
	 *
	 * @return string
	 */
	public function toString(): string;

	/**
	 * Create the value from a string.
	 *
	 * @param string $string String to create value from.
	 * @return static
	 */
	public static function fromString(string $string): static;
}
