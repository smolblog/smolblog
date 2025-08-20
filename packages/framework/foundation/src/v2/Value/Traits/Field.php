<?php

namespace Smolblog\Foundation\v2\Value\Traits;

use Stringable;

/**
 * A class that should be serialized to a single string, not an object.
 *
 * This is useful for types like UUIDs, email addresses, and other specific types of data.
 */
interface Field extends Stringable {
	public function toString(): string;
	public static function fromString(string $serialized): static;
}
