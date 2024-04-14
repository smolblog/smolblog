<?php

namespace Smolblog\Core\ContentV1\Queries;

use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Get a given Content object as a full Content object.
 */
class ContentById extends AdaptableContentQuery {
	/**
	 * Create the query.
	 *
	 * If the content is unpublished (or has unpublished edits), a user ID will be required to view the unpublished
	 * info.
	 *
	 * @param Identifier      $id     ID for the content.
	 * @param Identifier      $siteId ID of the site to search.
	 * @param Identifier|null $userId Optional user making the query.
	 */
	public function __construct(
		public readonly Identifier $id,
		public readonly Identifier $siteId,
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
