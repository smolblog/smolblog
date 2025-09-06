<?php

namespace Smolblog\Foundation\v2\Validation;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;

interface Validated {
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
	public function validate(): void;
}
