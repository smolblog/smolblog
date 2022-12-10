<?php

namespace Smolblog\Framework;

use Stringable;

/**
 * Represents an object that can be uniquely identified.
 */
abstract class Entity extends Value implements Stringable {
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
	 * Create the Entity. This constructor exists mostly for use by subclasses.
	 *
	 * @param Identifier $id Unique identification for this object.
	 */
	public function __construct(public readonly Identifier $id) {
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
		$fields = parent::toArray();
		$fields['id'] = strval($this->id);
		return $fields;
	}
}
