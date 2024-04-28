<?php

namespace Smolblog\Core\Media;

use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;

/**
 * Fetch the file from the given URL and add it to the media library.
 */
class SideloadMedia extends Command implements AuthorizableMessage {
	/**
	 * Create the command.
	 *
	 * @param string          $url               File to sideload.
	 * @param Identifier      $userId            User uploading the file.
	 * @param Identifier      $siteId            Site file is being uploaded to.
	 * @param string|null     $accessibilityText Alt text.
	 * @param string|null     $title             Title of the media.
	 * @param Identifier|null $mediaId           ID for the new media; will auto-generate if not given.
	 */
	public function __construct(
		public readonly string $url,
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public readonly ?string $accessibilityText = null,
		public readonly ?string $title = null,
		public readonly ?Identifier $mediaId = new DateIdentifier(),
	) {
	}

	/**
	 * Get the authorization query: user must have authorship permissions.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): UserHasPermissionForSite {
		return new UserHasPermissionForSite(siteId: $this->siteId, userId: $this->userId, mustBeAuthor: true);
	}
}
