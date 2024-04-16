<?php

namespace Smolblog\Core\Connector\Services;

use Psr\Container\ContainerInterface;
use Smolblog\Core\Connector\Connector;
use Smolblog\Foundation\Service\Registry\Registry;
use Smolblog\Foundation\Service\Registry\RegistryKit;

/**
 * Class to handle storing Connectors for use later.
 */
class ConnectorRegistry implements Registry {
	use RegistryKit {
		get as baseGet;
	}

	/**
	 * Available connectors with push capabilities.
	 *
	 * @var string[]
	 */
	public readonly array $pushConnectors;

	/**
	 * Available connectors with pull capabilities.
	 *
	 * @var string[]
	 */
	public readonly array $pullConnectors;

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
		$pushConnectors = [];
		$pullConnectors = [];

		foreach ($configuration as $className) {
			$config = $className::getConfiguration();
			$this->library[$config->key] = $className;

			if ($config->pushEnabled) {
				$pushConnectors[] = $config->key;
			}
			if ($config->pullEnabled) {
				$pullConnectors[] = $config->key;
			}
		}

		$this->pushConnectors = $pushConnectors;
		$this->pullConnectors = $pullConnectors;
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
