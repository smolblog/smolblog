<?php

namespace Smolblog\Core\Content\Media;

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Save an uploaded file to the media library
 */
class HandleUploadedMedia extends Command implements AuthorizableMessage {
	/**
	 * Create the command.
	 *
	 * @param UploadedFileInterface $file              Uploaded file.
	 * @param Identifier            $userId            User uploading the file.
	 * @param Identifier            $siteId            Site file is being uploaded to.
	 * @param string                $accessibilityText Alt text.
	 * @param string|null           $title             Title of the media.
	 * @param Identifier|null       $contentId         ID for the new media; will auto-generate if not given.
	 */
	public function __construct(
		public readonly UploadedFileInterface $file,
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public readonly ?string $accessibilityText,
		public readonly ?string $title = null,
		public readonly ?Identifier $contentId = new DateIdentifier(),
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
