<?php

namespace Smolblog\Core\Permissions;

use Smolblog\Foundation\Value\Fields\Identifier;

interface GlobalPermissionsService {
	/**
	 * Can the given user create a new site?
	 *
	 * @param Identifier $userId User to check.
	 * @return boolean
	 */
	public function canCreateSite(Identifier $userId): bool;
}
