<?php

namespace Smolblog\Core;

interface EndpointParameter {
	/**
	 * A text identifier for this parameter to use in the route or query string.
	 *
	 * @return string
	 */
	public function slug(): string;

	/**
	 * A regular expression to validate the parameter. Often required by URL params.
	 *
	 * @return string
	 */
	public function regex(): string;

	/**
	 * Validate the parameter. Given the passed value, the function should return
	 * `true` if the value is valid, `false` if not. If the parameter is not
	 * present in the request, `$given_value` will be `null`.
	 *
	 * A `false` return from this function should result in a 400 Bad Request
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
