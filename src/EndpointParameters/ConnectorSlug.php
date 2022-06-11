<?php

namespace Smolblog\Core\EndpointParameters;

class ConnectorSlug extends StringParameter {
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
}
