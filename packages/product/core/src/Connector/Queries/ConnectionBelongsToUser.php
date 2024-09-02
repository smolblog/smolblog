<?php

namespace Smolblog\Core\Connector\Queries;

use Smolblog\Framework\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Returns a truthy value if the given Connection belongs to the given User.
 */
class ConnectionBelongsToUser extends Query {
	/**
	 * Construct the Query
	 *
	 * @param Identifier $connectionId ID of the Connection to check.
	 * @param Identifier $userId       ID of the User to check.
	 */
	public function __construct(
		public readonly Identifier $connectionId,
		public readonly Identifier $userId,
	) {
	}
}
