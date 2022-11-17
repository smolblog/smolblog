<?php

namespace Smolblog\App\Registrars;

use Smolblog\Core\Connector\Connector;
use Smolblog\Core\Connector\ConnectorRegistrar as DomainConnectorRegistrar;

/**
 * Class to handle storing Connectors for use later.
 */
class ConnectorRegistrar extends GenericRegistrar implements DomainConnectorRegistrar {
	/**
	 * Fully-qualified name for the interface to check against.
	 *
	 * @var string
	 */
	protected string $interface = Connector::class;

	/**
	 * Get the Connector indicated by the given key.
	 *
	 * @param string $key Key for class to instantiate and get.
	 * @return Connector Requested Connector instance.
	 */
	public function get(string $key): Connector {
		return parent::get($key);
	}
}
