<?php

namespace Smolblog\Core\Permissions;

use Cavatappi\Foundation\Service;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Permissions\SitePermissionsService;
use Smolblog\Core\Site\Data\SiteUserRepo;
use Smolblog\Core\Site\Entities\SitePermissionLevel;
use Smolblog\Core\User\InternalSystemUser;
use Smolblog\Core\User\UserRepo;

/**
 * You should not type hint against this. Use GlobalPermissionsService and SitePermissionsService.
 *
 * A simple service that translates the broad strokes of "Author" and "Administrator" into the fine-grained permissions
 * defined in the permissions service interfaces.
 */
class DefaultPermissionsService implements SitePermissionsService, GlobalPermissionsService, Service {
	public function __construct(
		private SiteUserRepo $siteUserRepo,
		private UserRepo $userRepo,
	) {}

	private function isSuperAdmin(UuidInterface $userId): bool {
		if ($userId->toString() === InternalSystemUser::ID) {
			return true;
		}

		$user = $this->userRepo->userById($userId);
		return isset($user) ? $user->sudo : false;
	}

	private function sitePermissions(UuidInterface $userId, UuidInterface $siteId): SitePermissionLevel {
		if ($this->isSuperAdmin($userId)) {
			return SitePermissionLevel::Admin;
		}

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
		return in_array($this->sitePermissions($userId, $siteId), [SitePermissionLevel::Author, SitePermissionLevel::Admin]);
	}

	/**
	 * Can the given user edit all content on the given site (not just their own)?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canEditAllContent(UuidInterface $userId, UuidInterface $siteId): bool {
		return $this->sitePermissions($userId, $siteId) === SitePermissionLevel::Admin;
	}

	/**
	 * Can the given user add and remove channels for the given site?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canManageChannels(UuidInterface $userId, UuidInterface $siteId): bool {
		return $this->sitePermissions($userId, $siteId) === SitePermissionLevel::Admin;
	}

	/**
	 * Can the given user upload media to the given site?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canUploadMedia(UuidInterface $userId, UuidInterface $siteId): bool {
		return in_array($this->sitePermissions($userId, $siteId), [SitePermissionLevel::Author, SitePermissionLevel::Admin]);
	}

	/**
	 * Can the given user edit all media on the given site (not just their own)?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canEditAllMedia(UuidInterface $userId, UuidInterface $siteId): bool {
		return $this->sitePermissions($userId, $siteId) === SitePermissionLevel::Admin;
	}

	/**
	 * Can the given user push content to channels?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canPushContent(UuidInterface $userId, UuidInterface $siteId): bool {
		return in_array($this->sitePermissions($userId, $siteId), [SitePermissionLevel::Author, SitePermissionLevel::Admin]);
	}

	/**
	 * Can the given user set user permissions?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canManagePermissions(UuidInterface $userId, UuidInterface $siteId): bool {
		return $this->sitePermissions($userId, $siteId) === SitePermissionLevel::Admin;
	}

	/**
	 * Can the given user change site settings?
	 *
	 * @param UuidInterface $userId User to check.
	 * @param UuidInterface $siteId Site to check.
	 * @return boolean
	 */
	public function canManageSettings(UuidInterface $userId, UuidInterface $siteId): bool {
		return $this->sitePermissions($userId, $siteId) === SitePermissionLevel::Admin;
	}

	/**
	 * Can the given user create a new site?
	 *
	 * @param UuidInterface $userId User to check.
	 * @return boolean
	 */
	public function canCreateSite(UuidInterface $userId): bool {
		return $this->isSuperAdmin($userId);
	}

	/**
	 * Can the given user register a new user?
	 *
	 * @param UuidInterface $userId User to check.
	 * @return boolean
	 */
	public function canRegisterUser(UuidInterface $userId): bool {
		return $this->isSuperAdmin($userId) || $this->userRepo->hasUserWithId($userId);
	}

	/**
	 * Can the given user give another user super admin?
	 *
	 * @param UuidInterface $userId User to check.
	 * @return boolean
	 */
	public function canGrantUserSudo(UuidInterface $userId): bool {
		return $this->isSuperAdmin($userId);
	}

	/**
	 * Can the given user manage other users' Connections?
	 *
	 * @param UuidInterface $userId User to check.
	 * @return boolean
	 */
	public function canManageOtherConnections(UuidInterface $userId): bool {
		return $this->isSuperAdmin($userId);
	}
}
