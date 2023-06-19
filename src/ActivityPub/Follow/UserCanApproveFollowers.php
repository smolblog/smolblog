<?php

namespace Smolblog\ActivityPub\Follow;

use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;

/**
 * Returns a truthy value if the given user can approve ActivityPub follow requests.
 */
class UserCanApproveFollowers extends Query {
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
