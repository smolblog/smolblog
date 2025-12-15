<?php

namespace Smolblog\Core\Permissions;

use Ramsey\Uuid\UuidInterface;

/**
 * Check permissions related to individual Sites.
 */
interface SitePermissionsService {
	/**
	 * Can the given user create content on the given site?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canCreateContent(UuidInterface $userId, UuidInterface $siteId): bool;

	/**
	 * Can the given user edit all content on the given site (not just their own)?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canEditAllContent(UuidInterface $userId, UuidInterface $siteId): bool;

	/**
	 * Can the given user add and remove channels for the given site?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canManageChannels(UuidInterface $userId, UuidInterface $siteId): bool;

	/**
	 * Can the given user upload media to the given site?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canUploadMedia(UuidInterface $userId, UuidInterface $siteId): bool;

	/**
	 * Can the given user edit all media on the given site (not just their own)?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canEditAllMedia(UuidInterface $userId, UuidInterface $siteId): bool;

	/**
	 * Can the given user push content to channels?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canPushContent(UuidInterface $userId, UuidInterface $siteId): bool;

	/**
	 * Can the given user set user permissions?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canManagePermissions(UuidInterface $userId, UuidInterface $siteId): bool;

	/**
	 * Can the given user change site settings?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canManageSettings(UuidInterface $userId, UuidInterface $siteId): bool;
}
