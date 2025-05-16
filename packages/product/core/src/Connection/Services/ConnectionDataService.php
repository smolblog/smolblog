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
	 * @param Identifier $connectionUserId User being queried.
	 * @param Identifier $userId           User making the request.
	 * @return array
	 */
	public function connectionsForUser(Identifier $connectionUserId, Identifier $userId): array {
		if ($connectionUserId != $userId && !$this->perms->canManageOtherConnections($userId)) {
			return [];
		}

		return $this->repo->connectionsForUser($connectionUserId);
	}
}
