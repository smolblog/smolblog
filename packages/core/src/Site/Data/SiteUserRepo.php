<?php

namespace Smolblog\Core\Site\Data;

use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Site\Entities\SitePermissionLevel;

interface SiteUserRepo {
	/**
	 * Return true if a user with the given ID is permissioned for the given site.
	 *
	 * If there is an entry but the user's permissions are 'None', this should return false.
	 *
	 * @param UuidInterface $userId ID to check.
	 * @param UuidInterface $siteId ID to check.
	 * @return boolean
	 */
	public function hasUserForSite(UuidInterface $userId, UuidInterface $siteId): bool;

	/**
	 * Get the permssions for a given user for the given site.
	 *
	 * If no permissions have been set, the implementing class should return ::None.
	 *
	 * @param UuidInterface $userId ID to check.
	 * @param UuidInterface $siteId ID to check.
	 * @return SitePermissionLevel
	 */
	public function permissionsForUser(UuidInterface $userId, UuidInterface $siteId): SitePermissionLevel;
}
