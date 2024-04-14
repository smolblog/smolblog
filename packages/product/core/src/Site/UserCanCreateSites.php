<?php

namespace Smolblog\Core\Site;

use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Determine whether the given User is permitted to create a new Site.
 */
readonly class UserCanCreateSites extends Query {
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
