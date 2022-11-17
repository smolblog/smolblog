<?php

namespace Smolblog\Core\Connector;

use Smolblog\Framework\Registrar;

/**
 * Class to handle storing Connectors for use later.
 */
interface ConnectorRegistrar extends Registrar {
	/**
	 * Get the Connector indicated by the given key.
	 *
	 * @param string $key Key for class to instantiate and get.
	 * @return mixed Instance of the requested class.
	 */
	public function get(string $key): Connector;
}
