<?php

namespace Smolblog\Core\Media;

use Smolblog\Core\Media\Queries\UserCanEditMedia;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Messages\Query;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Framework\Messages\Query as MessagesQuery;

/**
 * Delete a media object.
 */
class DeleteMedia extends Command implements AuthorizableMessage {
	/**
	 * Construct the command.
	 *
	 * @param Identifier $userId  User making this change.
	 * @param Identifier $mediaId ID of the media being edited.
	 */
	public function __construct(
		public readonly Identifier $userId,
		public readonly Identifier $mediaId
	) {
	}

	/**
	 * Determine if the user owns the media object or is an admin on the site.
	 *
	 * @return UserCanEditMedia
	 */
	public function getAuthorizationQuery(): UserCanEditMedia {
		return new UserCanEditMedia(userId: $this->userId, mediaId: $this->mediaId);
	}
}
