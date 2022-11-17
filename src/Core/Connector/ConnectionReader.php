<?php

namespace Smolblog\Core\Connector;

use Smolblog\Framework\Reader;

/**
 * An object that can retrieve an Connection from a repository.
 */
interface ConnectionReader extends Reader {
	/**
	 * Get the indicated Connection from the repository. Should return null if not found.
	 *
	 * @param string|integer $id Unique identifier for the object.
	 * @return Connection Object identified by $id; null if it does not exist.
	 */
	public function get(string|int $id): Connection;

	/**
	 * Get the Connections that belong to the given User.
	 *
	 * @param string|integer $userId ID of the User to search on.
	 * @return array
	 */
	public function getConnectionsForUser(string|int $userId): array;
}
