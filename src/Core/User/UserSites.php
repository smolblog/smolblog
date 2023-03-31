<?php

namespace Smolblog\Core\User;

use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;

/**
 * Get the sites this user is attached to.
 */
class UserSites extends Query {
	/**
	 * Construct the query.
	 *
	 * @param Identifier $userId User whose sites to query.
	 */
	public function __construct(
		public readonly Identifier $userId
	) {
	}
}
