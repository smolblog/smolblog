<?php

namespace Smolblog\Framework\Objects;

/**
 * Allow a Value object to take extra variables at runtime.
 */
trait ExtendableValueKit {
	/**
	 * Additional variables defined at construction.
	 *
	 * @var array
	 */
	private array $extendedFields = [];

	/**
	 * Load the information in
	 *
	 * @param mixed ...$extended Arbitrary variables.
	 */
	public function __construct(mixed ...$extended) {
		$this->extendedFields = $extended;
	}

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
