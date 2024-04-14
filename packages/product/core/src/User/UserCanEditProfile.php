<?php

namespace Smolblog\Core\User;

use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Find out if the given user can edit the given profile.
 */
readonly class UserCanEditProfile extends Query {
	/**
	 * Create the query.
	 *
	 * @param Identifier $profileId ID of the user whose profile is being edited.
	 * @param Identifier $userId    ID of the user making the change.
	 */
	public function __construct(
		public readonly Identifier $profileId,
		public readonly Identifier $userId,
	) {
	}
}
