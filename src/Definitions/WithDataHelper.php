<?php

namespace Smolblog\Core\Definitions;

/**
 * For an object that has a DataHelper.
 */
interface WithDataHelper {
	/**
	 * Undocumented function
	 *
	 * @param DataHelper $helper Helper to link to this object.
	 * @return void
	 */
	public function setHelper(DataHelper $helper): void;

	/**
	 * Get all data from the model for saving to the persistant store.
	 *
	 * @return array
	 */
	public function getDataForStore(): array;

	/**
	 * Set data from the persistant store. After this method, `needsSave()`
	 * should be `false`.
	 *
	 * @param array $data Keys and their values from the store.
	 * @return void
	 */
	public function setDataFromStore(array $data): void;
}
