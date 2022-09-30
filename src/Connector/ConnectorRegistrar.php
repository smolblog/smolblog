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
	 * @throws RegistrationException Throws if required keys are not in $config.
	 * @param array $config Configuration array from the class.
	 * @return string Key to retrieve the class with.
	 */
	protected function processConfig(array $config): string {
		if (!isset($config['slug'])) {
			throw new RegistrationException('Connector config must include "slug".');
		}

		return $config['slug'];
	}
}
