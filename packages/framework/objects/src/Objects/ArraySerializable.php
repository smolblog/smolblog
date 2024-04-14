<?php

namespace Smolblog\Framework\Objects;

interface SerializeableValue {
	/**
	 * Create an instance of this class from an associative array.
	 *
	 * @param array $data Data to initialize class with.
	 * @return static New instancce of this object
	 */
	public static function deserializeValue(array $data): static;

	/**
	 * Get all defined fields as a single array.
	 *
	 * @return array
	 */
	public function serializeValue(): array;
}
