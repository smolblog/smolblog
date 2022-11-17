<?php

namespace Smolblog\Framework;

use JsonSerializable;

/**
 * Read-only data structure.
 *
 * Useful for passing data around from object to object. Value objects are intended to be immutable (and will be given
 * the `readonly` property when PHP 8.2 comes around).
 *
 * Declaring `readonly` properties in a defined object allows PHP to typecheck the object instead of relying on arrays
 * with specific keys.
 */
abstract class Value implements JsonSerializable {
	/**
	 * Create an instance of this class from a JSON string.
	 *
	 * @throws \JsonException If JSON decoding fails.
	 * @param string $json Valid JSON-formatted string that can create this object.
	 * @return static New instance of this object.
	 */
	public static function jsonDeserialize(string $json): static {
		$parsed = json_decode(json: $json, associative: true, flags: JSON_THROW_ON_ERROR);
		return static::fromArray(data: $parsed);
	}

	/**
	 * Create an instance of this class from an associative array. Assumes array keys map correctly to object
	 * properties.
	 *
	 * @param array $data Data to initialize class with.
	 * @return static New instancce of this object
	 */
	public static function fromArray(array $data): static {
		return new static(...$data);
	}

	/**
	 * Override `__set` to do nothing.
	 *
	 * @param string $name  Variable to set.
	 * @param mixed  $value Value to provide.
	 * @return void
	 */
	public function __set(string $name, mixed $value): void {
		$trace = debug_backtrace();
		trigger_error(
			'Attempt to set readonly property ' . $name .
			' in ' . $trace[0]['file'] .
			' on line ' . $trace[0]['line'],
			E_USER_ERROR
		);
	}

	/**
	 * Get all defined fields as a single array.
	 *
	 * @return array
	 */
	public function toArray(): array {
		return get_object_vars($this);
	}

	/**
	 * Same as toArray()
	 *
	 * @return mixed
	 */
	public function jsonSerialize(): mixed {
		return $this->toArray();
	}
}
