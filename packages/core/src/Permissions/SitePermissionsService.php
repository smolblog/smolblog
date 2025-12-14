<?php

namespace Smolblog\Core\Permissions;

use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Check permissions related to individual Sites.
 */
interface SitePermissionsService {
	/**
	 * Can the given user create content on the given site?
	 *
	 * @param Identifier $userId User to check.
	 * @param Identifier $siteId Site to check.
	 * @return boolean
	 */
	public function canCreateContent(Identifier $userId, Identifier $siteId): bool;

	/**
	 * Can the given user edit all content on the given site (not just their own)?
	 *
	 * @param Identifier $userId User to check.
	 * @param Identifier $siteId Site to check.
	 * @return boolean
	 */
	public function canEditAllContent(Identifier $userId, Identifier $siteId): bool;

	/**
	 * Can the given user add and remove channels for the given site?
	 *
	 * @param Identifier $userId User to check.
	 * @param Identifier $siteId Site to check.
	 * @return boolean
	 */
	public function canManageChannels(Identifier $userId, Identifier $siteId): bool;

	/**
	 * Can the given user upload media to the given site?
	 *
	 * @param Identifier $userId User to check.
	 * @param Identifier $siteId Site to check.
	 * @return boolean
	 */
	public function canUploadMedia(Identifier $userId, Identifier $siteId): bool;

	/**
	 * Can the given user edit all media on the given site (not just their own)?
	 *
	 * @param Identifier $userId User to check.
	 * @param Identifier $siteId Site to check.
	 * @return boolean
	 */
	public function canEditAllMedia(Identifier $userId, Identifier $siteId): bool;

	/**
	 * Can the given user push content to channels?
	 *
	 * @param Identifier $userId User to check.
	 * @param Identifier $siteId Site to check.
	 * @return boolean
	 */
	public function canPushContent(Identifier $userId, Identifier $siteId): bool;

	/**
	 * Can the given user set user permissions?
	 *
	 * @param Identifier $userId User to check.
	 * @param Identifier $siteId Site to check.
	 * @return boolean
	 */
	public function canManagePermissions(Identifier $userId, Identifier $siteId): bool;

	/**
	 * Can the given user change site settings?
	 *
	 * @param Identifier $userId User to check.
	 * @param Identifier $siteId Site to check.
	 * @return boolean
	 */
	public function canManageSettings(Identifier $userId, Identifier $siteId): bool;
}
