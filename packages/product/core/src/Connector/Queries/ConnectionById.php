<?php

namespace Smolblog\Core\Connector\Queries;

use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Find a Connection via its ID.
 *
 * If the Channel's provider and provider key are known, the Connection ID can be created using the static
 * Smolblog\Core\Connector\Entities\Connection::buildId function.
 */
readonly class ConnectionById extends Query {
	/**
	 * Construct the query
	 *
	 * @param Identifier $connectionId ID of the connection to fetch.
	 */
	public function __construct(
		public readonly Identifier $connectionId,
	) {
	}
}
