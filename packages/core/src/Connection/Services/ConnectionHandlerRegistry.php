<?php

namespace Smolblog\Core\Connection\Services;

use Cavatappi\Foundation\Registry\Registry;
use Cavatappi\Foundation\Registry\ServiceRegistryKit;
use Cavatappi\Foundation\Service;
use Psr\Container\ContainerInterface;

/**
 * Class to handle storing Connectors for use later.
 */
class ConnectionHandlerRegistry implements Registry, Service {
	use ServiceRegistryKit;

	/**
	 * This Registry is for Connectors.
	 *
	 * @return string
	 */
	public static function getInterfaceToRegister(): string {
		return ConnectionHandler::class;
	}

	/**
	 * Construct the Registrar with a DI container
	 *
	 * @param ContainerInterface $container Containter which contains the needed classes.
	 */
	public function __construct(ContainerInterface $container) {
		$this->container = $container;
	}

	/**
	 * Get a ConnectionHandler from the registry
	 *
	 * @param string $key Key for the ConnectionHandler.
	 * @return ConnectionHandler
	 */
	public function get(string $key): ConnectionHandler {
		return $this->getService($key);
	}

	/**
	 * List the available Connection handlers.
	 *
	 * @return string[]
	 */
	public function availableConnectionHandlers(): array {
		return array_keys($this->library);
	}
}
