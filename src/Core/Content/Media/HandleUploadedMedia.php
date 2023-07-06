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
	 * @param UploadedFileInterface $file              Uploaded file.
	 * @param Identifier            $userId            User uploading the file.
	 * @param Identifier            $siteId            Site file is being uploaded to.
	 * @param string                $title             Title of the media.
	 * @param string                $accessibilityText Alt text.
	 * @param string                $attribution       Any required attribution for the image.
	 */
	public function __construct(
		public readonly UploadedFileInterface $file,
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public readonly ?string $title = null,
		public readonly ?string $accessibilityText = null,
		public readonly ?string $attribution = null,
	) {
	}

	/**
	 * Store the created Media object for communication back to the application.
	 *
	 * @var Media|null
	 */
	public ?Media $createdMedia = null;

	/**
	 * Send the final URL to the uploaded, unaltered file. Used primarily for the micropub endpoint.
	 *
	 * @var string|null
	 */
	public ?string $urlToOriginal = null;

	/**
	 * Get the authorization query: user must have authorship permissions.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): UserHasPermissionForSite {
		return new UserHasPermissionForSite(siteId: $this->siteId, userId: $this->userId, mustBeAuthor: true);
	}
}
