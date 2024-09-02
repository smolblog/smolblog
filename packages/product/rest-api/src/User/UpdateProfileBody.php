<?php

namespace Smolblog\Api\User;

use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Framework\Objects\Value;

/**
 * Expected body for an Update Profile request.
 */
class UpdateProfileBody extends Value {
	/**
	 * Construct the payload.
	 *
	 * @param string|null $handle      Unique, human-readable identifier for this user. Omit for no change.
	 * @param string|null $displayName Name to display for the user. Omit for no change.
	 * @param string|null $pronouns    How the user wants to be identified. Omit for no change.
	 */
	public function __construct(
		public readonly ?string $handle = null,
		public readonly ?string $displayName = null,
		public readonly ?string $pronouns = null,
	) {
	}
}
