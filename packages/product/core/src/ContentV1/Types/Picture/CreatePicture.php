<?php

namespace Smolblog\Core\ContentV1\Types\Picture;

use Smolblog\Core\Site\UserHasPermissionForSite;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\Command;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\DateIdentifier;
use Smolblog\Framework\Objects\Identifier;

/**
 * Create a Picture.
 */
class CreatePicture extends Command implements AuthorizableMessage {
	/**
	 * Media to display.
	 *
	 * @var Identifier[]
	 */
	public readonly array $mediaIds;

	/**
	 * Construct the command.
	 *
	 * @param Identifier   $siteId    Site for this picture.
	 * @param Identifier   $userId    User authoring this picture.
	 * @param Identifier[] $mediaIds  Media to display.
	 * @param string|null  $caption   Caption for the picture.
	 * @param Identifier   $contentId ID for the new picture; will auto-generate if not given.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		array $mediaIds,
		public readonly ?string $caption = null,
		public readonly Identifier $contentId = new DateIdentifier(),
	) {
		$this->mediaIds = array_values($mediaIds);
	}

	/**
	 * User must be able to author posts on this site.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new UserHasPermissionForSite(
			siteId: $this->siteId,
			userId: $this->userId,
			mustBeAuthor: true,
			mustBeAdmin: false,
		);
	}
}
