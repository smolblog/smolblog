<?php

namespace Smolblog\Core\EndpointParameters;

use Smolblog\Core\Registrars\ConnectorRegistrar;

/**
 * Specialized parameter for requiring a slug that resolves to a valid
 * Connector.
 */
class ConnectorSlug extends BasicParameter {
	/**
	 * Name for this parameter.
	 *
	 * @var string
	 */
	protected string $name = 'slug';

	/**
	 * True if this is a required parameter.
	 *
	 * @var boolean
	 */
	protected bool $isRequired = true;

	/**
	 * Validate that a Connector exists for the given parameter
	 *
	 * @param mixed $given_value Value of this parameter as given in the request.
	 * @return boolean true if this is a valid value.
	 */
	protected function extendedValidation(mixed $given_value = null): bool {
		try {
			strval($given_value);
		} catch (Throwable $e) {
			// If there is an exception raised during `strval`, then it's definitely not good.
			return false;
		}

		return null !== ConnectorRegistrar::retrieve(slug: $given_value);
	}

	/**
	 * Redefine the constructor with all defaults.
	 *
	 * @param string  $name         Name of the parameter. Default 'slug'.
	 * @param boolean $isRequired   True if this parameter is required. Default true.
	 * @param mixed   $defaultValue Default value if none is provided.
	 */
	public function __construct(string $name = 'slug', bool $isRequired = true, mixed $defaultValue = null) {
		parent::__construct(name: $name, isRequired: $isRequired, defaultValue: $defaultValue);
	}
}
