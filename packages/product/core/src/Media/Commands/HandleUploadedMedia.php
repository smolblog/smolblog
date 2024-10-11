<?php

namespace Smolblog\Core\Media\Commands;

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\Command;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;

/**
 * Save an uploaded file to the media library
 */
class HandleUploadedMedia extends Command implements AuthorizableMessage {
	/**
	 * Create the command.
	 *
	 * @throws InvalidValueProperties When no accessibility text is provided.
	 *
	 * @param UploadedFileInterface $file              Uploaded file.
	 * @param Identifier            $userId            User uploading the file.
	 * @param Identifier            $siteId            Site file is being uploaded to.
	 * @param string|null           $accessibilityText Alt text.
	 * @param string|null           $title             Title of the media.
	 * @param Identifier|null       $mediaId           ID for the new media; will auto-generate if not given.
	 */
	public function __construct(
		public UploadedFileInterface $file,
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public ?string $accessibilityText = null,
		public ?string $title = null,
		public Identifier $mediaId = new DateIdentifier(),
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