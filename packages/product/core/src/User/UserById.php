<?php

namespace Smolblog\Core\User;

use Smolblog\Framework\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Retrieve a User by ID
 */
class UserById extends Query {
	/**
	 * Construct the query
	 *
	 * @param Identifier $userId ID of the user.
	 */
	public function __construct(
		public readonly Identifier $userId
	) {
	}
}
