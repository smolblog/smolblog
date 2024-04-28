<?php

namespace Smolblog\Core\Media;

use Smolblog\Core\Media\Queries\UserCanEditMedia;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;

/**
 * Change the attributes on a media object.
 */
class EditMediaAttributes extends Command implements AuthorizableMessage {
	/**
	 * Construct the command.
	 *
	 * @throws InvalidValueProperties Thrown if no updated attributes are given.
	 *
	 * @param Identifier  $mediaId           ID of the media being edited.
	 * @param Identifier  $siteId            ID of the site holding the media.
	 * @param Identifier  $userId            User making this change.
	 * @param string|null $title             New title.
	 * @param string|null $accessibilityText New alt text.
	 */
	public function __construct(
		public readonly Identifier $mediaId,
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly ?string $title = null,
		public readonly ?string $accessibilityText = null,
	) {
		if (!isset($title) && !isset($accessibilityText)) {
			throw new InvalidValueProperties('No updated attributes provided.');
		}
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
