<?php

namespace Smolblog\Core\Site\Data;

use Smolblog\Core\Site\Entities\UserSitePermissions;
use Smolblog\Foundation\Value\Fields\Identifier;

interface SiteRepo {
	/**
	 * Get permissions for the given user and site.
	 *
	 * @param Identifier $userId User to check.
	 * @param Identifier $siteId Site to check.
	 * @return UserSitePermissions|null
	 */
	public function userPermissionsForSite(Identifier $userId, Identifier $siteId): ?UserSitePermissions;
}
