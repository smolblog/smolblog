<?php

namespace Smolblog\Core\Definitions;

/**
 * Represents an object that can load and save an object's data.
 */
interface DataHelper {
	/**
	 * Use `$usingParameters` to find the data for `$object` and load it in.
	 *
	 * @param WithDataHelper $object          Object to save data to.
	 * @param array          $usingParameters Parameters used to find data.
	 * @return boolean true if successfully found and loaded
	 */
	public function setDataFor(WithDataHelper $object, array $usingParameters): bool;

	/**
	 * Get the data from `$object` and save it.
	 *
	 * @param WithDataHelper $object Object to get data from.
	 * @return boolean true if successfully saved
	 */
	public function saveDataFor(WithDataHelper $object): bool;
}
