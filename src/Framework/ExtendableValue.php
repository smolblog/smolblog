<?php

namespace Smolblog\Framework;

/**
 * Value object that can take extra variables at runtime.
 */
abstract class ExtendableValue extends Value {
	/**
	 * Additional variables defined at construction.
	 *
	 * @var array
	 */
	protected array $extendedFields = [];

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
		$publicFields = parent::toArray();
		unset($publicFields['extendedFields']);
		return [...$publicFields, ...$this->extendedFields];
	}
}
