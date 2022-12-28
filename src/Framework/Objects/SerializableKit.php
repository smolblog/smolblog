<?php

namespace Smolblog\Framework\Objects;

/**
 * Provide simple array serialization functions to objects.
 *
 * Client classes will implement JsonSerializable and ArraySerializable.
 */
trait SerializableKit {
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
