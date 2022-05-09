<?php

namespace Smolblog\Core\Definitions;

/**
 * Represents an object with data backed by a persistent store of some kind.
 */
interface Model {
	/**
	 * Initialize a empty instance of the model ready for data.
	 *
	 * @return self
	 */
	public static function init(): self;

	/**
	 * Initialize model by finding an instance from the store with the given
	 * properties. If no such instance is found, function should return `null`
	 * unless `$create` is passed as `true`, in which case the function should
	 * create a new instance with the given properties set.
	 *
	 * @param array   $properties Array of keys and their values needed.
	 * @param boolean $create     True to create new instance if needed.
	 * @return self|null
	 */
	public static function initWithProperties(array $properties, bool $create = false): self|null;

	/**
	 * Find out if this instance's data is out-of-sync with the persistant store.
	 *
	 * @return boolean
	 */
	public function needsSave(): bool;

	/**
	 * Send this model's data back to the persistant store.
	 *
	 * @return void
	 */
	public function save(): void;
}
