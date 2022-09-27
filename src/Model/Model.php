<?php

namespace Smolblog\Core;

use Smolblog\Core\Environment;
use Smolblog\Core\Definitions\ModelField;
use Smolblog\Core\Exceptions\ModelException;

/**
 * An object backed by a persistant data store of some kind.
 */
abstract class Model {
	/**
	 * Store the ModelHelper for this instance.
	 *
	 * @var ModelHelper
	 */
	protected ModelHelper $helper;

	/**
	 * Store the data for this Model.
	 *
	 * @var array
	 */
	protected array $data = [];

	/**
	 * List of valid fields for the model. Used to verify get/set method. Name of
	 * the field should be the key, value should be a ModelField.
	 *
	 * @var ModelField[]
	 */
	public const FIELDS = [];

	/**
	 * True if the values in $data have been modified since the last save()
	 *
	 * @var boolean
	 */
	protected bool $isDirty = true;

	/**
	 * Construct a new Model with the given ModelHelper.
	 *
	 * @param ModelHelper|null $withHelper Helper for this instance.
	 */
	public function __construct(ModelHelper $withHelper) {
		$this->helper = $withHelper;
	}

	/**
	 * Standard getter; gets attribute from $data if it exists
	 *
	 * @param string $name Property to get.
	 * @return mixed|null Value of $data[$name] or null
	 */
	public function __get(string $name) {
		if (!in_array($name, array_keys(static::FIELDS))) {
			$trace = debug_backtrace();
			trigger_error(
				'Undefined property ' . $name .
				' accessed in ' . $trace[0]['file'] .
				' on line ' . $trace[0]['line'],
				E_USER_NOTICE
			);
			return null;
		}

		return $this->data[ $name ] ?? null;
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
		$error = $this->fieldValidationErrorMessage($name, $value);
		if (isset($error)) {
			$trace = debug_backtrace();
			trigger_error(
				'Invalid value ' . $value .
				' for field ' . $name .
				' set in ' . $trace[0]['file'] .
				' on line ' . $trace[0]['line'] .
				'; ' . $error,
				E_USER_NOTICE
			);
			return;
		}

		$this->data[$name] = $value;
		$this->isDirty = true;
	}

		/**
		 * Validate incoming data.
		 *
		 * @param string $name  Property to set.
		 * @param mixed  $value Value to set.
		 * @return string|null null if valid, error message if not
		 */
	protected function fieldValidationErrorMessage(string $name, mixed $value): ?string {
		$fieldType = static::FIELDS[$name] ?? null;

		if (!isset($fieldType)) {
			return "$name is not a field.";
		}

		switch ($fieldType) {
			case ModelField::int:
				if (!is_int($value)) {
					return "$name must be an integer.";
				}
				return null;
			case ModelField::string:
				try {
					strval($value);
				} catch (Throwable $e) {
					return "$name must be stringable.";
				}
				return null;
			case ModelField::float:
				if (!is_float($value)) {
					return "$name must be a real number.";
				}
				return null;
		}
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
	 * @param mixed $id Primary key for this model.
	 * @return boolean False if ID is not found
	 */
	public function loadWithId(mixed $id): bool {
		$dataFromHelper = $this->helper->getData(forModel: $this, withId: $id);

		if ($dataFromHelper) {
			$this->data = $dataFromHelper;
			$this->isDirty = false;
			return true;
		}

		return false;
	}
}
