<?php

namespace Smolblog\Core\Connector;

use Psr\Container\ContainerInterface;
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
	 * @param ContainerInterface $container Containter which contains the needed classes.
	 */
	public function __construct(ContainerInterface $container) {
		$this->container = $container;
		$this->interface = Connector::class;
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
