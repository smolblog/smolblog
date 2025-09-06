<?php

namespace Smolblog\Foundation\v2\Validation;

use ReflectionAttribute;
use ReflectionClass;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;

trait ValidatedKit {
	/**
	 * Validate the object and throw an exception if conditions are not met.
	 *
	 * This is for (de)serialization, cloning, or any other object creation method that bypasses the constructor. This
	 * method should be called from the constructor after all necessary properties are set.
	 *
	 * @throws InvalidValueProperties When the object does not pass validation.
	 *
	 * @return void
	 */
	public function validate(): void {
		$classReflection = new ReflectionClass($this);

		$this->checkOneOfAttributes($classReflection);
	}

	private function checkOneOfAttributes(ReflectionClass $classReflection): void {
		$maybeAtLeastOne = $classReflection->getAttributes(AtLeastOneOf::class, ReflectionAttribute::IS_INSTANCEOF);
		if (!empty($maybeAtLeastOne)) {
			$atLeastOne = $maybeAtLeastOne[0]->newInstance();
			// Using strict comparision instead of isset() in case $prop is virtual.
			if (\array_all($atLeastOne->properties, fn($prop) => $this->$prop === null)) {
				throw new InvalidValueProperties(
					'At least one of these properties must be set: ' . \implode(',', $atLeastOne->properties)
				);
			}
		}

		$maybeOnlyOne = $classReflection->getAttributes(OnlyOneOf::class, ReflectionAttribute::IS_INSTANCEOF);
		if (!empty($maybeOnlyOne)) {
			$onlyOne = $maybeOnlyOne[0]->newInstance();
			// Using strict comparision instead of isset() in case $prop is virtual.
			if (\array_filter($onlyOne->properties, fn($prop) => $this->$prop !== null)) {
				throw new InvalidValueProperties(
					'Only one of these properties must be set: ' . \implode(',', $onlyOne->properties)
				);
			}
		}
	}
}
