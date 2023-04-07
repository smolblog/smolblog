<?php

namespace Smolblog\Api\Site;

use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Payload for setting user permissions on a site.
 */
class UserPermissionPayload extends Value {
	/**
	 * Construct the payload.
	 *
	 * @param Identifier $userId   ID of the user.
	 * @param boolean    $isAdmin  True if user should be an administrator.
	 * @param boolean    $isAuthor True if user should be able to author content.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly bool $isAdmin = false,
		public readonly bool $isAuthor = false,
	) {
	}
}
