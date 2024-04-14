<?php

namespace Smolblog\Core\User;

use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Get the sites this user is attached to.
 */
readonly class UserSites extends Query {
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
