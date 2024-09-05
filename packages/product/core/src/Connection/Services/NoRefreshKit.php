<?php

namespace Smolblog\Core\Connection\Services;

use Smolblog\Core\Connection\Entities\Connection;

/**
 * Trait for Connectors that do not deal with refreshable tokens.
 *
 * Fulfills the connectionNeedsRefresh and refreshConnection methods of the Connector interface by always returning
 * `false` for needing a refresh and the unchanged Connection for refreshing the Connection.
 */
trait NoRefreshKit {
	/**
	 * Check the connection to see if it needs to be refreshed.
	 *
	 * @param Connection $connection Connection object to check.
	 * @return boolean true if Connection requires a refresh.
	 */
	public function connectionNeedsRefresh(Connection $connection): bool {
		return false;
	}

	/**
	 * Refresh the given Connection and return the updated object.
	 *
	 * @param Connection $connection Connection object to refresh.
	 * @return Connection Refreshed Connection.
	 */
	public function refreshConnection(Connection $connection): Connection {
		return $connection;
	}
}
