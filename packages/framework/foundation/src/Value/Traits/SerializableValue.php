<?php

namespace Smolblog\Foundation\Value\Traits;

use JsonSerializable;

/**
 * Indicates an object that can be converted to and from an array of native PHP values.
 */
interface SerializableValue extends JsonSerializable {
	/**
	 * Serialize this object to a key/value array or a scalar value.
	 *
	 * All object properties should be fully serialized themselves such that the values are either scalar values or
	 * key/value arrays themselves. The object should be able to be re-created in full from this serialized version.
	 *
	 * @return mixed
	 */
	public function serializeValue(): mixed;

	/**
	 * Create an object from an array.
	 *
	 * It is assumed that the array passed to this function is intended to deserialize to the given class. If it cannot,
	 * it is recommended to throw an InvalidValueProperties exception.
	 *
	 * @param array $data Serialized object.
	 * @return static
	 */
	public static function deserializeValue(array $data): static;

	/**
	 * Serialize the object to an array that can be converted to JSON.
	 *
	 * @return mixed
	 */
	public function jsonSerialize(): mixed;

	/**
	 * Serialize the object to JSON.
	 *
	 * @return string
	 */
	public function toJson(): string;

	/**
	 * Deserialize the object from JSON.
	 *
	 * @param string $json Serialized object as a JSON-formatted string.
	 * @return static
	 */
	public static function fromJson(string $json): static;
}
