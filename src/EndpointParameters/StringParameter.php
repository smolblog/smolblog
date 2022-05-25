<?php

namespace Smolblog\Core\EndpointParameters;

use Throwable;
use Smolblog\Core\EndpointParameter;

/**
 * EndpointParameter that parses the value as a string
 */
class StringParameter extends EndpointParameter {
	/**
	 * Validate that the given value _can_ be a string
	 *
	 * @param mixed $given_value Value of this parameter as given in the request.
	 * @return boolean true if this is a valid value.
	 */
	protected function extendedValidation(mixed $given_value = null): bool {
		try {
			strval($given_value);
		} catch (Throwable $e) {
			// If there is an exception raised during `strval`, then it won't convert.
			return false;
		}
		return true;
	}

	/**
	 * Turn the given value into a string
	 *
	 * @param mixed $given_value Value of this parameter as given in the request.
	 * @return integer Parsed value of the parameter.
	 */
	protected function extendedParsing(mixed $given_value = null): string {
		return strval($given_value);
	}
}
