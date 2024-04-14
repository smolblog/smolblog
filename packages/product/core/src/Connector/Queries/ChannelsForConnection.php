<?php

namespace Smolblog\Core\Connector\Queries;

use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Find all Channels for a Connection. Returns an array of Channel objects.
 *
 * If the Connection's provider and provider key are known, the Connection ID can be created using the static
 * Smolblog\Core\Connector\Entities\Connection::buildId function.
 */
readonly class ChannelsForConnection extends Query {
	/**
	 * Construct the query
	 *
	 * @param Identifier $connectionId ID of the Connection to fetch Channels for.
	 */
	public function __construct(
		public readonly Identifier $connectionId,
	) {
	}
}
