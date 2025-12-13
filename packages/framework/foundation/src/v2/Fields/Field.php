<?php

namespace Smolblog\Foundation\v2\Fields;

use Stringable;

/**
 * A class that should be serialized to a single string, not an object.
 *
 * This is useful for types like UUIDs, email addresses, and other specific types of data.
 */
interface Field extends Stringable {
	/**
	 * Serialize this object to a string.
	 *
	 * @return string
	 */
	public function __toString(): string;

	/**
	 * Create this object from a string.
	 *
	 * @param string $serialized Serialized value.
	 * @return static
	 */
	public static function fromString(string $serialized): static;
}
