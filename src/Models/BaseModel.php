<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Definitions\ModelHelper;

/**
 * An object backed by a persistant data store of some kind.
 */
class BaseModel {
	/**
	 * Store the ModelHelper for this instance.
	 *
	 * @var ModelHelper|null
	 */
	protected ?ModelHelper $helper;

	/**
	 * Store the data for this Model.
	 *
	 * @var array
	 */
	protected array $data = [];

	/**
	 * True if the values in $data have been modified since the last save()
	 *
	 * @var boolean
	 */
	protected bool $isDirty = false;

	/**
	 * Construct a new Model with the given ModelHelper.
	 *
	 * @param ModelHelper|null $withModel Helper for this instance.
	 */
	public function __construct(?ModelHelper $withModel = null) {
		$this->helper = $withModel;
		$this->loadInitData();
	}

	/**
	 * Standard getter; gets attribute from $data if it exists
	 *
	 * @param string $name Property to get.
	 * @return mixed|null Value of $data[$name] or null
	 */
	public function __get(string $name) {
		if (!isset($this->data[ $name ])) {
			$trace = debug_backtrace();
			trigger_error(
				'Undefined property ' . $name .
				' accessed in ' . $trace[0]['file'] .
				' on line ' . $trace[0]['line'],
				E_USER_NOTICE
			);
			return null;
		}

		return $this->data[ $name ];
	}

	/**
	 * Standard setter. Sets given attribute to $data and marks
	 * instance as dirty.
	 *
	 * @param string $name  Property to set.
	 * @param mixed  $value Value to set.
	 * @return void
	 */
	public function __set(string $name, mixed $value): void {
		if (! isset($this->data[ $name ])) {
			$trace = debug_backtrace();
			trigger_error(
				'Undefined property ' . $name .
				' set in ' . $trace[0]['file'] .
				' on line ' . $trace[0]['line'],
				E_USER_NOTICE
			);
			return;
		}

		$this->data[ $name ] = $value;
		$this->isDirty       = true;
	}

	/**
	 * Called during construction. Loads data from helper by default.
	 *
	 * @return void
	 */
	protected function loadInitData(): void {
		$this->data = $this->helper->getData();
	}
}
