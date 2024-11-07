<?php

namespace Smolblog\Foundation\Value\Traits;

use Smolblog\Foundation\Value;

/**
 * Trait to proivde string methods for Values that are scalars. Pair with the Field interface.
 */
trait FieldKit {
	use SerializableValueKit;

	/**
	 * Compare serialized values to determine equality.
	 *
	 * @param Value $other Object to compare to.
	 * @return boolean
	 */
	public function equals(Value $other): bool {
		return is_a($other, Field::class) && strval($this) == strval($other);
	}

	/**
	 * Provide the serialized value as the string representation.
	 *
	 * @return string
	 */
	abstract public function toString(): string;

	/**
	 * Create the value from a string.
	 *
	 * @param string $string String to create value from.
	 * @return static
	 */
	abstract public static function fromString(string $string): static;

	/**
	 * Provide the serialized value as the string representation.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return $this->toString();
	}

	/**
	 * Serialize the value to a string.
	 *
	 * @return string
	 */
	public function serializeValue(): string {
		return $this->toString();
	}

	/**
	 * Deserialize the value from a string.
	 *
	 * @param mixed $string String to deserialize.
	 * @return static
	 */
	public static function deserializeValue(mixed $string): static {
		return static::fromString(strval($string));
	}
}
