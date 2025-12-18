<?php

namespace Smolblog\Core\Connection\Data;

use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Connection\Entities\Connection;

/**
 * Retrieve Connections and information about them
 */
interface ConnectionRepo {
	/**
	 * Find out if the given Connection belongs to the given User.
	 *
	 * @param UuidInterface $connectionId Connection to check.
	 * @param UuidInterface $userId       User to check.
	 * @return boolean True if the given User created the given Connection.
	 */
	public function connectionBelongsToUser(UuidInterface $connectionId, UuidInterface $userId): bool;

	/**
	 * Fetch the given Connection from the repo; null if none is found.
	 *
	 * @param UuidInterface $connectionId Connection to fetch.
	 * @return Connection|null
	 */
	public function connectionById(UuidInterface $connectionId): ?Connection;

	/**
	 * Get all Connections for a given User.
	 *
	 * @param UuidInterface $userId User whose Connections are being fetched.
	 * @return Connection[]
	 */
	public function connectionsForUser(UuidInterface $userId): array;
}
