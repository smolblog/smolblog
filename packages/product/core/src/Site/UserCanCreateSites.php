<?php

namespace Smolblog\Core\Site;

use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;

/**
 * Determine whether the given User is permitted to create a new Site.
 */
class UserCanCreateSites extends Query {
	/**
	 * Construct the query.
	 *
	 * @param Identifier $userId User whose permissions to check.
	 */
	public function __construct(
		public readonly Identifier $userId
	) {
	}
}
