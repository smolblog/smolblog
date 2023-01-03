<?php

namespace Smolblog\Core\Connector\Services;

use Psr\Container\ContainerInterface;
use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\Hooks\CollectingConnectors;
use Smolblog\Framework\Messages\MessageBus;
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
	 * @param MessageBus         $messageBus MessageBus for dispatching the hook.
	 */
	public function __construct(ContainerInterface $container, MessageBus $messageBus) {
		$this->container = $container;
		$this->interface = Connector::class;

		$connectors = $messageBus->dispatch(new CollectingConnectors())->connectors;
		foreach ($connectors as $provider => $className) {
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
