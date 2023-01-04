<?php

namespace Smolblog\Framework\Objects;

use DateTimeImmutable;
use Throwable;

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

	// Utility functions.

	/**
	 * Attempt to create an Identifier from a string; gives null on failure.
	 *
	 * @param string $idString String to deserialize.
	 * @return Identifier|null
	 */
	public static function safeDeserializeIdentifier(string $idString): ?Identifier {
		if (empty($idString)) {
			return null;
		}
		$id = null;
		try {
			$id = Identifier::fromString($idString);
		} catch (Throwable $e) {
			// Do nothing; we knew this could fail.
		}
		return $id;
	}

	/**
	 * Attempt to create a DateTimeImmutable object from a date string; returns null on failure.
	 *
	 * @param string $dateString String to deserialize.
	 * @return DateTimeImmutable|null
	 */
	public static function safeDeserializeDate(string $dateString): ?DateTimeImmutable {
		if (empty($dateString)) {
			return null;
		}
		$date = null;
		try {
			$date = new DateTimeImmutable($dateString);
		} catch (Throwable $e) {
			// Do nothing; we knew this could fail.
		}
		return $date;
	}
}
