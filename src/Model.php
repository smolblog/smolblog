<?php

namespace Smolblog\Core;

use Smolblog\Core\Exceptions\ModelException;

/**
 * An object backed by a persistant data store of some kind.
 */
class Model {
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
	 * List of valid fields for the model. Used to verify get/set method.
	 *
	 * @var array
	 */
	protected array $fields = [];

	/**
	 * True if the values in $data have been modified since the last save()
	 *
	 * @var boolean
	 */
	protected bool $isDirty = true;

	/**
	 * Construct a new Model with the given ModelHelper.
	 *
	 * @throws ModelException If the Model requires a helper but does not get one.
	 * @param ModelHelper|null $withHelper Helper for this instance.
	 * @param array            $withData   Data to initialize/find model with.
	 */
	public function __construct(?ModelHelper $withHelper = null, array $withData = []) {
		$this->helper = $withHelper;
		$this->data = $withData;
		$this->loadInitData();
	}

	/**
	 * Standard getter; gets attribute from $data if it exists
	 *
	 * @param string $name Property to get.
	 * @return mixed|null Value of $data[$name] or null
	 */
	public function __get(string $name) {
		if (!in_array($name, $this->fields)) {
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
		if (!in_array($name, $this->fields)) {
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
	 * Instruct the Model's helper to save the current data state.
	 *
	 * @return void
	 */
	public function save(): void {
		if ($this->helper->save(model: $this, withData: $this->data)) {
			$this->isDirty = false;
		}
	}

	/**
	 * Returns true if this Model is out-of-sync with the persistent store.
	 *
	 * @return boolean
	 */
	public function needsSave(): bool {
		return $this->isDirty;
	}

	/**
	 * Called during construction. Loads data from helper by default.
	 *
	 * @throws ModelException If the Model requires a helper but does not have one.
	 * @return void
	 */
	protected function loadInitData(): void {
		if (!$this->helper) {
			throw new ModelException('A ModelHelper is required for this model.');
		}

		$dataFromHelper = $this->helper->getData(forModel: $this, withProperties: $this->data);
		if ($dataFromHelper) {
			$this->data = $dataFromHelper;
			$this->isDirty = false;
		}
	}
}
