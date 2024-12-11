<?php

namespace Smolblog\Core\User;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\EntityKit;

/**
 * Represents a User in the system.
 */
readonly class User extends Value implements Entity {
	use EntityKit;

	/**
	 * Construct the User
	 *
	 * @param Identifier $id          Permanent ID of the User.
	 * @param string     $key         Human readable handle for the user.
	 * @param string     $displayName Full name to display.
	 * @param string     $handler     Subsystem to handle authentication.
	 */
	public function __construct(
		Identifier $id,
		public string $key,
		public string $displayName,
		public string $handler,
	) {
		$this->id = $id;
	}
}
