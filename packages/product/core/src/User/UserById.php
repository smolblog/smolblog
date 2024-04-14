<?php

namespace Smolblog\Core\User;

use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Retrieve a User by ID
 */
readonly class UserById extends Query {
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
