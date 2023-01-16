<?php

namespace Smolblog\Core\Connector\Services;

use Psr\Container\ContainerInterface;
use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\Hooks\CollectingConnectors;
use Smolblog\Framework\Objects\RegistrarKit;

/**
 * Class to handle storing Connectors for use later.
 */
class ConnectorRegistrar {
	use RegistrarKit {
		get as baseGet;
	}

	/**
	 * Construct the Registrar with a DI container
	 *
	 * @param ContainerInterface $container  Containter which contains the needed classes.
	 * @param array         $configuration Array of key => service class to configure the registrar.
	 */
	public function __construct(ContainerInterface $container, array $configuration) {
		$this->container = $container;
		$this->interface = Connector::class;

		foreach ($configuration as $provider => $className) {
			$this->register(key: $provider, class: $className);
		}
	}

	/**
	 * Get the given Connector from the Registrar
	 *
	 * @param string $key Key for the Connector.
	 * @return Connector
	 */
	public function get(string $key): Connector {
		return $this->baseGet($key);
	}
}
