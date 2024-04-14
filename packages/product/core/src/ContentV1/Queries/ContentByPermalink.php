<?php

namespace Smolblog\Core\ContentV1\Queries;

use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Find a piece of content by its permalink.
 */
class ContentByPermalink extends AdaptableContentQuery {
	/**
	 * Create the query.
	 *
	 * If the content is unpublished (or has unpublished edits), a user ID will be required to view the unpublished
	 * info.
	 *
	 * @param Identifier      $siteId    ID of the site to search.
	 * @param string          $permalink Permalink for the content.
	 * @param Identifier|null $userId    Optional user making the query.
	 */
	public function __construct(
		public readonly Identifier $siteId,
		public readonly string $permalink,
		public readonly ?Identifier $userId = null,
	) {
	}

	/**
	 * Get the site being searcherd.
	 *
	 * @return Identifier
	 */
	public function getSiteId(): Identifier {
		return $this->siteId;
	}

	/**
	 * Get the user making this query.
	 *
	 * @return Identifier|null
	 */
	public function getUserId(): ?Identifier {
		return $this->userId;
	}
}
