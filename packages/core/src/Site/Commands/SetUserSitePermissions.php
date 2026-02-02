<?php

namespace Smolblog\Core\Site\Commands;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Site\Entities\SitePermissionLevel;

/**
 * Add (or change) permissions for a user on a site.
 *
 * The user must be an admin *unless* the user is changing their own permissions and not enabling admin permissions.
 */
readonly class SetUserSitePermissions implements Command, Authenticated {
	use ValueKit;

	/**
	 * Construct the command.
	 *
	 * @param UuidInterface       $siteId       ID of the site for the permissions.
	 * @param UuidInterface       $linkedUserId ID of the user whose permissions are being changed.
	 * @param UuidInterface       $userId       ID of the user making the change.
	 * @param SitePermissionLevel $level        Permssion level to set.
	 */
	public function __construct(
		public UuidInterface $siteId,
		public UuidInterface $linkedUserId,
		public UuidInterface $userId,
		public SitePermissionLevel $level,
	) {}
}
