<?php

namespace Smolblog\Core\Site\Commands;

use Smolblog\Core\Site\Entities\SitePermissionLevel;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;

/**
 * Add (or change) permissions for a user on a site.
 *
 * The user must be an admin *unless* the user is changing their own permissions and not enabling admin permissions.
 */
readonly class SetUserSitePermissions extends Command {
	/**
	 * Construct the command.
	 *
	 * @param Identifier          $siteId       ID of the site for the permissions.
	 * @param Identifier          $linkedUserId ID of the user whose permissions are being changed.
	 * @param Identifier          $userId       ID of the user making the change.
	 * @param SitePermissionLevel $level        Permssion level to set.
	 */
	public function __construct(
		public Identifier $siteId,
		public Identifier $linkedUserId,
		public Identifier $userId,
		public SitePermissionLevel $level,
	) {
		parent::__construct();
	}
}
