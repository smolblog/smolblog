<?php

namespace Smolblog\Core\Models;

use Smolblog\Core\Definitions\ModelHelper;
use Smolblog\Core\Exceptions\ModelException;

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
	 * @param ModelHelper|null $withModel Helper for this instance.
	 * @param array            $withData  Data to initialize/find model with.
	 */
	public function __construct(?ModelHelper $withModel = null, array $withData = []) {
		if (!$withModel) {
			throw new ModelException('A ModelHelper is required for this model.');
		}

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
	 * Called during construction. Loads data from helper by default.
	 *
	 * @return void
	 */
	protected function loadInitData(): void {
		$dataFromHelper = $this->helper->getData(forModel: $this, withProperties: $this->data);
		if ($dataFromHelper) {
			$this->data = $dataFromHelper;
			$this->isDirty = false;
		}
	}
}
