<?php

namespace Smolblog\Core\Permissions;

use Cavatappi\Foundation\Service;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Core\Site\Data\SiteUserRepo;
use Smolblog\Core\Site\Entities\SitePermissionLevel;

/**
 * You should not type hint against this.
 *
 * A simple service that translates the broad strokes of "Author" and "Administrator" into the fine-grained permissions
 * defined in the permissions service interfaces.
 */
class DefaultPermissionsService implements SitePermissionsService, Service {
	public function __construct(
		private SiteUserRepo $siteUserRepo
	)
	{
	}

	private function permissions(UuidInterface $userId, UuidInterface $siteId): SitePermissionLevel {
		return $this->siteUserRepo->permissionsForUser($userId, $siteId);
	}

	/**
	 * Can the given user create content on the given site?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canCreateContent(UuidInterface $userId, UuidInterface $siteId): bool {
		return in_array($this->permissions($userId, $siteId), [SitePermissionLevel::Author, SitePermissionLevel::Admin]);
	}

	/**
	 * Can the given user edit all content on the given site (not just their own)?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canEditAllContent(UuidInterface $userId, UuidInterface $siteId): bool {
		return $this->permissions($userId, $siteId) === SitePermissionLevel::Admin;
	}

	/**
	 * Can the given user add and remove channels for the given site?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canManageChannels(UuidInterface $userId, UuidInterface $siteId): bool {
		return $this->permissions($userId, $siteId) === SitePermissionLevel::Admin;
	}

	/**
	 * Can the given user upload media to the given site?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canUploadMedia(UuidInterface $userId, UuidInterface $siteId): bool {
		return in_array($this->permissions($userId, $siteId), [SitePermissionLevel::Author, SitePermissionLevel::Admin]);
	}

	/**
	 * Can the given user edit all media on the given site (not just their own)?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canEditAllMedia(UuidInterface $userId, UuidInterface $siteId): bool {
		return $this->permissions($userId, $siteId) === SitePermissionLevel::Admin;
	}

	/**
	 * Can the given user push content to channels?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canPushContent(UuidInterface $userId, UuidInterface $siteId): bool {
		return in_array($this->permissions($userId, $siteId), [SitePermissionLevel::Author, SitePermissionLevel::Admin]);
	}

	/**
	 * Can the given user set user permissions?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canManagePermissions(UuidInterface $userId, UuidInterface $siteId): bool {
		return $this->permissions($userId, $siteId) === SitePermissionLevel::Admin;
	}

	/**
	 * Can the given user change site settings?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canManageSettings(UuidInterface $userId, UuidInterface $siteId): bool {
		return $this->permissions($userId, $siteId) === SitePermissionLevel::Admin;
	}
}
