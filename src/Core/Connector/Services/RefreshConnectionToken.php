<?php

namespace Smolblog\Core\Connector\Services;

use Smolblog\Core\Connector\ConnectorRegistrar;
use Smolblog\Core\Connector\ConnectorWithRefresh;
use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Core\Connector\Entities\ConnectionWriter;
use Smolblog\Framework\Service;

/**
 * Service to check if a Connection needs a refresh and save the refreshed Connection if so.
 */
class RefreshConnectionToken implements Service {
	/**
	 * Construct the service
	 *
	 * @param ConnectorRegistrar $connectorRepo    Connectors to look up.
	 * @param ConnectionWriter   $connectionWriter Connection writer to save the changes.
	 */
	public function __construct(
		private ConnectorRegistrar $connectorRepo,
		private ConnectionWriter $connectionWriter,
	) {
	}

	/**
	 * Check the given Connection to see if it needs to be refreshed. If it does, refresh it and save the result.
	 *
	 * @param Connection $connection Connection object to check.
	 * @return Connection Connection object ready to be used.
	 */
	public function run(Connection $connection): Connection {
		$connector = $this->connectorRepo->get($connection->provider);
		if (
			!in_array(ConnectorWithRefresh::class, class_implements($connector)) ||
			!$connector->connectionNeedsRefresh($connection)
		) {
			return $connection;
		}

		$refreshed = $connector->refreshConnection($connection);
		$this->connectionWriter->save($refreshed);
		return $refreshed;
	}
}
