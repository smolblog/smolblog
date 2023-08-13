<?php

namespace Smolblog\Core\Content\Types\Picture;

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
	 * Construct the command.
	 *
	 * @param Identifier   $siteId     Site for this picture.
	 * @param Identifier   $userId     User authoring this picture.
	 * @param Identifier[] $mediaIds   Media to display.
	 * @param string|null  $caption    Caption for the picture.
	 * @param string|null  $givenTitle Optional title.
	 * @param Identifier   $contentId  ID for the new picture; will auto-generate if not given.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $userId,
		public readonly array $mediaIds,
		public readonly ?string $caption = null,
		public readonly ?string $givenTitle = null,
		public readonly Identifier $contentId = new DateIdentifier(),
	) {
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
