<?php

namespace Smolblog\Framework\Objects;

/**
 * Provides an Identifier property and extends SerializableKit to work with it.
 *
 * Entities are defined by their Identifier. This trait provides the readonly $id property and also extends the
 * fromArray and toArray functions to serialize the identifier to and from a standard UUID string.
 */
trait EntityKit {
	use SerializableKit;

	/**
	 * Unique identifier (UUID) for this particular entity.
	 *
	 * @var Identifier
	 */
	public readonly Identifier $id;

	/**
	 * Create an instance of this class from an associative array. Assumes array keys map correctly to object
	 * properties.
	 *
	 * @param array $data Data to initialize class with.
	 * @return static New instancce of this object
	 */
	public static function fromArray(array $data): static {
		$dataWithIdentifier = [...$data, 'id' => Identifier::fromString($data['id'])];
		return new static(...$dataWithIdentifier);
	}

	/**
	 * Returns the fully-qualified class name and the object's $id. Used in comparisons.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return static::class . ':' . strval($this->id);
	}

	/**
	 * Get all defined fields as a single array.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$fields = get_object_vars($this);
		$fields['id'] = strval($this->id);
		return $fields;
	}
}
