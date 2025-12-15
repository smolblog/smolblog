<?php

namespace Smolblog\Core\Content\Data;

use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Content\Entities\Content;

interface ContentRepo {
	/**
	 * Check if content with a given ID exists.
	 *
	 * No user check as this is intended to prevent ID collisions.
	 *
	 * @param UuidInterface $contentId ID to check.
	 * @return boolean
	 */
	public function hasContentWithId(UuidInterface $contentId): bool;

	/**
	 * Get a given Content object as a full Content object; null if not found.
	 *
	 * @param UuidInterface $contentId ID for the content.
	 * @return Content|null
	 */
	public function contentById(UuidInterface $contentId): ?Content;

	/**
	 * Retrieve a list of Content objects
	 *
	 * @param UuidInterface      $forSite     Content assigned to the given site.
	 * @param UuidInterface|null $ownedByUser Content owned by the given user.
	 * @return array Content objects meeting the given parameters.
	 */
	public function contentList(
		UuidInterface $forSite,
		?UuidInterface $ownedByUser = null,
	): array;
}
