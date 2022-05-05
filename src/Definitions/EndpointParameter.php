<?php

namespace Smolblog\Core\Definitions;

/**
 * Defines a parameter used by an Endpoint
 *
 * @see Smolblog\Core\Definitions\Endpoint
 */
interface EndpointParameter {
	/**
	 * Name for this parameter.
	 *
	 * @return string
	 */
	public function name(): string;

	/**
	 * Validate the parameter. Given the passed value, the function should return
	 * `true` if the value is valid, `false` if not. If the parameter is not
	 * present in the request, `$given_value` will be `null`.
	 *
	 * A `false` return from this function will result in a 400 Bad Request
	 * response.
	 *
	 * @param mixed $given_value Value of this parameter as given in the request.
	 * @return boolean true if this is a valid value.
	 */
	public function validate(mixed $given_value): bool;

	/**
	 * Parse the paramter. This could involve converting a string to integer, an
	 * id into a Model, or `null` (from an absent value) into a default value.
	 *
	 * @param mixed $given_value Value of this parameter as given in the request.
	 * @return mixed Parsed value of the parameter.
	 */
	public function parse(mixed $given_value): mixed;
}
