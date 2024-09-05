<?php

namespace Smolblog\Core\Connection\Data;

use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Retrieve Connections and information about them
 */
interface ConnectionRepo {
	/**
	 * Find out if the given Connection belongs to the given User.
	 *
	 * @param Identifier $connectionId Connection to check.
	 * @param Identifier $userId       User to check.
	 * @return boolean True if the given User created the given Connection.
	 */
	public function connectionBelongsToUser(Identifier $connectionId, Identifier $userId): bool;

	/**
	 * Fetch the given Connection from the repo; null if none is found.
	 *
	 * @param Identifier $connectionId Connection to fetch.
	 * @return Connection|null
	 */
	public function connectionById(Identifier $connectionId): ?Connection;

	/**
	 * Get all Connections for a given User.
	 *
	 * @param Identifier $userId User whose Connections are being fetched.
	 * @return Connection[]
	 */
	public function connectionsForUser(Identifier $userId): array;
}
