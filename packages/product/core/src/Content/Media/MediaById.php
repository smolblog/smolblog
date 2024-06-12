<?php

namespace Smolblog\Core\Content\Media;

use Smolblog\Core\Content\Queries\ContentVisibleToUser;
use Smolblog\Foundation\Value\Traits\AuthorizableMessage;
use Smolblog\Framework\Messages\MemoizableQuery;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Search for a given media by its ID.
 *
 * Does not extend BaseContentById as Media does not have the same type/extension considerations.
 */
class MediaById extends MemoizableQuery implements AuthorizableMessage {
	/**
	 * Construct the query
	 *
	 * @param Identifier      $siteId    ID of the site being queried.
	 * @param Identifier      $contentId ID of the media being queried.
	 * @param Identifier|null $userId    Optional user making the query.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $contentId,
		public readonly ?Identifier $userId = null,
	) {
	}

	/**
	 * See if the given user (if they are provided) can view the given media.
	 *
	 * @return ContentVisibleToUser
	 */
	public function getAuthorizationQuery(): ContentVisibleToUser {
		return new ContentVisibleToUser(
			siteId: $this->siteId,
			contentId: $this->contentId,
			userId: $this->userId,
		);
	}
}
