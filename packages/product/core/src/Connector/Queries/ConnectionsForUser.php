<?php

namespace Smolblog\Core\Connector\Queries;

use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Find the Connections assigned to a User and fetch them. Returns an array of Connection objects.
 *
 * If the Connection ID and channel Key are known, the Channel ID can be created using the static
 * Smolblog\Core\Connector\Entities\Channel::buildId function.
 */
readonly class ConnectionsForUser extends Query {
	/**
	 * Construct the query
	 *
	 * @param Identifier $userId ID of the user whose Connections to fetch.
	 */
	public function __construct(
		public readonly Identifier $userId,
	) {
	}
}
