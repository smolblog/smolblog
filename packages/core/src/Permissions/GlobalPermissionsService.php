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
	 * Can the given user manage other users' Connections?
	 *
	 * @param UuidInterface $userId User to check.
	 * @return boolean
	 */
	public function canManageOtherConnections(UuidInterface $userId): bool;
}
