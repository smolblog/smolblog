<?php

namespace Smolblog\Core\User;

use Smolblog\Framework\Exceptions\InvalidCommandParametersException;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Update a user's profile information.
 *
 * Yes, this command has both a user ID and profile ID. Just in case an admin needs to edit a profile.
 */
readonly class UpdateProfile extends Command implements AuthorizableMessage {
	/**
	 * Create the command.
	 *
	 * Must provide one of $handle, $displayName, or $pronouns.
	 *
	 * @throws InvalidCommandParametersException Thrown if a changed attribute is not provided.
	 *
	 * @param Identifier  $userId      ID of the user making the change.
	 * @param Identifier  $profileId   ID of the user whose profile is being edited.
	 * @param string|null $handle      Unique human-readable identifier for this user. Null for no change.
	 * @param string|null $displayName What the user would like to be called. Null for no change.
	 * @param string|null $pronouns    How the user would like to be identified. Null for no change.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly Identifier $profileId,
		public readonly ?string $handle = null,
		public readonly ?string $displayName = null,
		public readonly ?string $pronouns = null,
	) {
		if (!isset($this->handle) && !isset($this->displayName) && !isset($this->pronouns)) {
			throw new InvalidCommandParametersException(command: $this);
		}
	}

	/**
	 * Get authorization query for this command.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new UserCanEditProfile(
			profileId: $this->profileId,
			userId: $this->userId,
		);
	}
}
