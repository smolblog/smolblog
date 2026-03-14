<?php

namespace Smolblog\Core\User;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * Elevate a user to super-admin privileges.
 */
class GrantUserSudo implements Command, Authenticated {
	use ValueKit;

	public function __construct(
		public UuidInterface $userId,
		public UuidInterface $userIdToEscalate,
	) {}
}
