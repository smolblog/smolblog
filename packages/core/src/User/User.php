<?php

namespace Smolblog\Core\User;

use Cavatappi\Foundation\DomainEvent\Entity;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * Represents a User in the system.
 */
readonly class User implements Value, Entity {
	use ValueKit;

	/**
	 * Construct the User
	 *
	 * @param UuidInterface $id          Permanent ID of the User.
	 * @param string        $key         Human readable handle for the user.
	 * @param string        $displayName Full name to display.
	 * @param string        $handler     Subsystem to handle authentication.
	 */
	public function __construct(
		public UuidInterface $id,
		public string $key,
		public string $displayName,
		public string $handler,
	) {}
}
