<?php

namespace Smolblog\Core\Factories;

use Smolblog\Core\ModelHelper;
use Smolblog\Core\Models\Transient;

/**
 * Object for making Transient models.
 */
class TransientFactory {
	/**
	 * Create the factory
	 *
	 * @param ModelHelper $helper Helper to use when creating Transients.
	 */
	public function __construct(private ModelHelper $helper) {
	}

	/**
	 * Save a transient with the given information. If a transient with $name already exists, it will
	 * be overwritten.
	 *
	 * @param string  $name                   Key for the transient. Will overwrite any existing with this name.
	 * @param mixed   $value                  Serializable value to store.
	 * @param integer $secondsUntilExpiration How many seconds to keep the Transient in the data store. Default 300.
	 * @return void
	 */
	public function setTransient(string $name, mixed $value, int $secondsUntilExpiration = 300): void {
		$transient = new Transient(withHelper: $this->helper);
		$transient->loadWithId($name);
		$transient->value = $value;
		$transient->expires = time() + $secondsUntilExpiration;
		$transient->save();
	}

	/**
	 * Get a transient value if it exists.
	 *
	 * @param string $name Name of the transient.
	 * @return mixed Stored value; null if not found or expired.
	 */
	public function getTransient(string $name): mixed {
		$transient = new Transient(withHelper: $this->helper);
		$transient->loadWithId($name);

		if ($transient->expires ?? 0 < time()) {
			return null;
		}

		return $transient->value;
	}
}
