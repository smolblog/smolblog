<?php

namespace Smolblog\Core\Connector;

use Smolblog\Core\Connector\Entities\Connection;

/**
 * Class to use a refresh token to get a new authorization token. Extension to Connector.
 */
interface ConnectorWithRefresh extends Connector {
	/**
	 * Check the connection to see if it needs to be refreshed.
	 *
	 * @param Connection $connection Connection object to check.
	 * @return boolean true if Connection requires a refresh.
	 */
	public function connectionNeedsRefresh(Connection $connection): bool;

	/**
	 * Refresh the given Connection and return the updated object.
	 *
	 * @param Connection $connection Connection object to refresh.
	 * @return Connection Refreshed Connection.
	 */
	public function refreshConnection(Connection $connection): Connection;
}
