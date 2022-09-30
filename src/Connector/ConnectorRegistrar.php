<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Registrar\{Registrar, RegistrationException};

/**
 * Class to handle storing Connectors for use later.
 */
class ConnectorRegistrar extends Registrar {
	/**
	 * Handle the configuration of the Connector.
	 *
	 * @param mixed $config Configuration array from the class.
	 * @return string Key to retrieve the class with.
	 */
	protected function processConfig(mixed $config): string {
		return $config->slug;
	}
}
