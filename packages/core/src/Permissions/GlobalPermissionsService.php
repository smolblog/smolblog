<?php

namespace Smolblog\Core\Permissions;

use Ramsey\Uuid\UuidInterface;

interface GlobalPermissionsService {
	/**
	 * Can the given user create a new site?
	 *
	 * @param UuidInterface $userId User to check.
	 * @return boolean
	 */
	public function canCreateSite(UuidInterface $userId): bool;

	/**
	 * Can the given user register a new user?
	 *
	 * @param UuidInterface $userId User to check.
	 * @return boolean
	 */
	public function canRegisterUser(UuidInterface $userId): bool;

	/**
	 * Can the given user give another user super admin?
	 *
	 * @param UuidInterface $userId User to check.
	 * @return boolean
	 */
	public function canGrantUserSudo(UuidInterface $userId): bool;

	/**
	 * Can the given user manage other users' Connections?
	 *
	 * @param UuidInterface $userId User to check.
	 * @return boolean
	 */
	public function canManageOtherConnections(UuidInterface $userId): bool;
}
