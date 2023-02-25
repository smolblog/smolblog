<?php

namespace Smolblog\Core\Connector\Services;

use Psr\Container\ContainerInterface;
use Smolblog\Core\Connector\Connector;
use Smolblog\Framework\Infrastructure\Registry;
use Smolblog\Framework\Objects\RegistrarKit;

/**
 * Class to handle storing Connectors for use later.
 */
class ConnectorRegistry implements Registry {
	use RegistrarKit {
		get as baseGet;
	}

	/**
	 * This Registry is for Connectors.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return Connector::class;
	}

	/**
	 * Construct the Registrar with a DI container
	 *
	 * @param ContainerInterface $container     Containter which contains the needed classes.
	 * @param array              $configuration Array of class names to configure the registrar.
	 */
	public function __construct(ContainerInterface $container, array $configuration) {
		$this->container = $container;
		$this->interface = Connector::class;

		foreach ($configuration as $className) {
			$this->library[$className::getSlug()] = $className;
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
