<?php

namespace Smolblog\Core;

// This sniff apparently does not support `readonly`.
//phpcs:disable Squiz.Commenting.VariableComment.Missing

/**
 * Defines a parameter used by an Endpoint
 */
class EndpointParameter {
	/**
	 * Name for this parameter.
	 *
	 * @var string
	 */
	public readonly string $name;

	/**
	 * True if this is a required parameter.
	 *
	 * @var boolean
	 */
	protected bool $isRequired = false;

	/**
	 * Default value if no value is supplied
	 *
	 * @var mixed
	 */
	protected mixed $defaultValue = null;

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
	public function validate(mixed $given_value): bool {
		if (!isset($given_value) && $this->isRequired) {
			return false;
		}

		return $this->extendedValidation();
	}

	/**
	 * Additional hook for subclasses to validate after presence is validated.
	 *
	 * @param mixed $given_value Value of this parameter as given in the request.
	 * @return boolean true if this is a valid value.
	 */
	protected function extendedValidation(mixed $given_value): bool {
		return true;
	}

	/**
	 * Parse the paramter. This could involve converting a string to integer, an
	 * id into a Model, or `null` (from an absent value) into a default value.
	 *
	 * @param mixed $given_value Value of this parameter as given in the request.
	 * @return mixed Parsed value of the parameter.
	 */
	public function parse(mixed $given_value): mixed {
		if (!isset($given_value) && isset($this->defaultValue)) {
			return $this->defaultValue;
		}

		return $this->extendedParsing($given_value);
	}

	/**
	 * Additional hook for subclasses to parse the value after the default is handled.
	 *
	 * @param mixed $given_value Value of this parameter as given in the request.
	 * @return mixed Parsed value of the parameter.
	 */
	protected function extendedParsing(mixed $given_value): mixed {
		return $given_value;
	}

	/**
	 * Build this EndpointParameter
	 *
	 * @param string  $name         Name of the parameter.
	 * @param boolean $isRequired   True if this parameter is required. Default false.
	 * @param mixed   $defaultValue Default value if none is provided.
	 */
	public function __construct(string $name, bool $isRequired = false, mixed $defaultValue = null) {
		$this->name = $name;
		$this->isRequired = $isRequired;
		$this->defaultValue = $defaultValue;
	}
}
