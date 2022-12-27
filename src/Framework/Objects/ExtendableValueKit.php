<?php

namespace Smolblog\Framework\Objects;

/**
 * Allow a Value object to take extra variables at runtime.
 */
trait ExtendableValueKit {
	use ValueKit;

	/**
	 * Additional variables defined at construction.
	 *
	 * @var array
	 */
	private array $extendedFields = [];

	/**
	 * Quick access for any added variables.
	 *
	 * @param string $name Variable to get.
	 * @return mixed Value of the variable or null if not found.
	 */
	public function __get(string $name): mixed {
		return $this->extendedFields[$name] ?? null;
	}

	/**
	 * Override `__set` to do nothing. Will remove when PHP 8.2 is required.
	 *
	 * @param string $name  Variable to set.
	 * @param mixed  $value Value to provide.
	 * @return void
	 */
	public function __set(string $name, mixed $value): void {
		$trace = debug_backtrace();
		trigger_error(
			'Attempt to set readonly property ' . $name .
			' in ' . $trace[0]['file'] .
			' on line ' . $trace[0]['line'],
			E_USER_ERROR
		);
	}

	/**
	 * Get all defined fields as a single array.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$publicFields = get_object_vars($this);
		unset($publicFields['extendedFields']);
		return [...$publicFields, ...$this->extendedFields];
	}
}
