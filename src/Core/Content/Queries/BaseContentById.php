<?php

namespace Smolblog\Core\Content\Queries;

use Smolblog\Core\Content\ContentBuilder;
use Smolblog\Core\Content\ContentBuilderKit;
use Smolblog\Framework\Messages\AuthorizableMessage;
use Smolblog\Framework\Messages\MemoizableQuery;
use Smolblog\Framework\Messages\Query;
use Smolblog\Framework\Objects\Identifier;

/**
 * Base class for singluar content queries.
 *
 * Sets up memoization, content extensions, and a visibility security check.
 *
 * Use GenericContentById for a concrete query.
 */
abstract class BaseContentById extends MemoizableQuery implements ContentBuilder, AuthorizableMessage {
	use ContentBuilderKit;

	/**
	 * Construct the query.
	 *
	 * @param Identifier      $siteId    ID of the site to pull from.
	 * @param Identifier      $contentId ID of the content being queried.
	 * @param Identifier|null $userId    Optional user making the request.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly Identifier $contentId,
		public readonly ?Identifier $userId = null
	) {
	}

	/**
	 * Get the authorization query and check if the given user can see the given content.
	 *
	 * @return Query
	 */
	public function getAuthorizationQuery(): Query {
		return new ContentVisibleToUser(siteId: $this->siteId, contentId: $this->contentId, userId: $this->userId);
	}
}
