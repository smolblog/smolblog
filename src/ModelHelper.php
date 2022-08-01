<?php

namespace Smolblog\Core;

use Smolblog\Core\Model;

/**
 * Used to handle interactions between a Model and a persistant data store.
 */
interface ModelHelper {
	/**
	 * Get data from the persistent store for the given model.
	 *
	 * @param Model|null $forModel Model to get data for.
	 * @param mixed      $withId   Primary key(s) to search for in the persistent store; default none.
	 * @return array|null Associative array of the model's data; null if data is not in store.
	 */
	public function getData(Model $forModel = null, mixed $withId = null): ?array;

	/**
	 * Save the given data from the given model to the persistent store.
	 *
	 * It is recommended that the implementing class throw a ModelException if there is an unexpected error.
	 *
	 * @param Model|null $model    Model to save data for.
	 * @param array      $withData Data from the model to save.
	 * @return boolean True if save was successful.
	 */
	public function save(Model $model = null, array $withData = []): bool;
}
