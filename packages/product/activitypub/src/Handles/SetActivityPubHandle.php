<?php

namespace Smolblog\ActivityPub\Handles;

use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;
use Smolblog\Framework\Objects\RandomIdentifier;

/**
 * Set the ActivityPub handle for a site.
 */
class SetActivityPubHandle extends Command implements AuthorizableMessage {
	/**
	 * Construct the command.
	 *
	 * @param string     $handle   ActivityPub handle.
	 * @param Identifier $siteId   ID of the site this handle belongs to.
	 * @param Identifier $userId   ID of the user making this change.
	 * @param Identifier $handleId ID for this handle.
	 */
	public function __construct(
		public readonly string $handle,
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly Identifier $handleId = new RandomIdentifier(),
	) {
	}

	/**
	 * Get the authorization query for this command.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new UserHasPermissionForSite(siteId: $this->siteId, userId: $this->userId, mustBeAdmin: true);
	}
}
