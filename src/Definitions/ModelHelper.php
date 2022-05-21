<?php

namespace Smolblog\Core\Definitions;

use Smolblog\Core\Models\BaseModel;

/**
 * Used to handle interactions between a Model and a persistant data store.
 */
interface ModelHelper {
	/**
	 * Get data from the persistent store for the given model.
	 *
	 * @param BaseModel|null $forModel       Model to get data for.
	 * @param array          $withProperties Properties to search for in the persistent store; default none.
	 * @return array|null Associative array of the model's data; null if data is not in store.
	 */
	public function getData(BaseModel $forModel = null, array $withProperties = []): ?array;

	/**
	 * Save the given data from the given model to the persistent store.
	 *
	 * It is recommended that the implementing class throw a ModelException if there is an unexpected error.
	 *
	 * @param BaseModel|null $model    Model to save data for.
	 * @param array          $withData Data from the model to save.
	 * @return boolean True if save was successful.
	 */
	public function save(BaseModel $model = null, array $withData = []): bool;
}
