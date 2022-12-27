<?php

namespace Smolblog\Framework\Objects;

use JsonSerializable;

/**
 * Read-only data structure.
 *
 * Useful for passing data around from object to object. Value objects are intended to be immutable (and will be given
 * the `readonly` property when PHP 8.2 comes around). The methods useful for Values can also be used elsewhere, so it
 * may be better to just use the ValueKit trait based on your use case (ex: an AuthorizableMessage must have a
 * mutable property).
 *
 * Declaring `readonly` properties in a defined object allows PHP to typecheck the object instead of relying on arrays
 * with specific keys.
 */
abstract class Value implements JsonSerializable {
	use ValueKit;

	/**
	 * Override `__set` to do nothing. Will remove when PHP 8.2 is required.
	 *
	 * @param string $name  Variable to set.
	 * @param mixed  $value Value to provide.
	 * @return void
	 */
	public function __set(string $name, mixed $value): void {
		$trace = debug_backtrace();
		trigger_error(
			'Attempt to modify read-only object ' .
			' in ' . $trace[0]['file'] .
			' on line ' . $trace[0]['line'],
			E_USER_ERROR
		);
	}
}
