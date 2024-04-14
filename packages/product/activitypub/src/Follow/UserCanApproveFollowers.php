<?php

namespace Smolblog\ActivityPub\Follow;

use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Returns a truthy value if the given user can approve ActivityPub follow requests.
 */
readonly class UserCanApproveFollowers extends Query {
	/**
	 * Construct the query.
	 *
	 * @param Identifier $userId User with permissions.
	 * @param Identifier $siteId Site being edited.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
	) {
	}
}
