<?php

namespace Smolblog\Core\Connection\Services;

use Smolblog\Core\Connection\Data\ConnectionRepo;
use Smolblog\Core\Permissions\GlobalPermissionsService;
use Smolblog\Foundation\Service;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Get Connection information with security checks.
 */
class ConnectionDataService implements Service {
	/**
	 * Construct the service.
	 *
	 * @param ConnectionRepo           $repo  Base Connection data store.
	 * @param GlobalPermissionsService $perms Permissions to check.
	 */
	public function __construct(private ConnectionRepo $repo, private GlobalPermissionsService $perms) {
	}

	/**
	 * Get all Connections owned by a user. Current user must be the user or have canManageOtherConnections.
	 *
	 * @param Identifier $userId        User being queried.
	 * @param Identifier $currentUserId User making the request.
	 * @return array
	 */
	public function connectionsForUser(Identifier $userId, Identifier $currentUserId): array {
		if ($userId != $currentUserId && !$this->perms->canManageOtherConnections($currentUserId)) {
			return [];
		}

		return $this->repo->connectionsForUser($userId);
	}
}
