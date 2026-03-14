<?php

namespace Smolblog\Core\User;

use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Command\ExpectedResponse;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * Add a user to the system.
 */
#[ExpectedResponse(type: UuidInterface::class, name: 'id', description: 'ID of the created user')]
class RegisterUser implements Command, Authenticated {
	use ValueKit;

	public function __construct(
		public UuidInterface $userId,
		public string $key,
		public string $displayName,
		public ?UuidInterface $newUserId = null,
	)
	{
	}
}
