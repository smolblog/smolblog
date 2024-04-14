<?php

namespace Smolblog\Core\Site;

use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Add (or change) permissions for a user on a site.
 *
 * The user must be an admin *unless* the user is changing their own permissions and not enabling admin permissions.
 */
readonly class LinkSiteAndUser extends Command implements AuthorizableMessage {
	/**
	 * Construct the command.
	 *
	 * @param Identifier $siteId        ID of the site for the permissions.
	 * @param Identifier $linkedUserId  ID of the user whose permissions are being changed.
	 * @param Identifier $commandUserId ID of the user making the change.
	 * @param boolean    $isAuthor      True if user should have author permissions on this site.
	 * @param boolean    $isAdmin       True if user should have admin permissions on this site.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $linkedUserId,
		public readonly Identifier $commandUserId,
		public readonly bool $isAuthor = false,
		public readonly bool $isAdmin = false,
	) {
	}

	/**
	 * Check the permissions for this command.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		$needAuthor = false;
		$needAdmin = true;
		if ($this->linkedUserId == $this->commandUserId && !$this->isAdmin) {
			$needAdmin = false;
			$needAuthor = true;
		}

		return new UserHasPermissionForSite(
			userId: $this->commandUserId,
			siteId: $this->siteId,
			mustBeAdmin: $needAdmin,
			mustBeAuthor: $needAuthor,
		);
	}
}
