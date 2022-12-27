<?php

namespace Smolblog\Framework\Objects;

/**
 * Useful methods for Value-style objects.
 *
 * Serialization and deserialization methods for Value and Value-like objects (objects largely consisting of public
 * properties).
 */
trait ValueKit {
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
	 * Create a new Value object from this one with the given properties changed.
	 *
	 * @param mixed ...$newValues Propertes to replace.
	 * @return static
	 */
	public function newWith(mixed ...$newValues): static {
		$merged = [...get_object_vars($this), ...$newValues];
		return new static(...$merged);
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
