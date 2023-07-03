<?php

namespace Smolblog\Core\Content\Media;

use Psr\Http\Message\UploadedFileInterface;
use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;

/**
 * Save an uploaded file to the media library
 */
class HandleUploadedMedia extends Command implements AuthorizableMessage {
	/**
	 * Create the command.
	 *
	 * @param UploadedFileInterface $file   Uploaded file.
	 * @param Identifier            $userId User uploading the file.
	 * @param Identifier            $siteId Site file is being uploaded to.
	 */
	public function __construct(
		public readonly UploadedFileInterface $file,
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
	) {
	}

	/**
	 * Store the created Media object for communication back to the application.
	 *
	 * @var Media|null
	 */
	public ?Media $createdMedia = null;

	/**
	 * Get the authorization query: user must have authorship permissions.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): UserHasPermissionForSite {
		return new UserHasPermissionForSite(siteId: $this->siteId, userId: $this->userId, mustBeAuthor: true);
	}
}
