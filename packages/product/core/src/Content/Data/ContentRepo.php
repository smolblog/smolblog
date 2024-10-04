<?php

namespace Smolblog\Core\Content\Data;

use Smolblog\Core\Content\Entities\Content;
use Smolblog\Foundation\Value\Fields\Identifier;

interface ContentRepo {
	/**
	 * Check if content with a given ID exists.
	 *
	 * No user check as this is intended to prevent ID collisions.
	 *
	 * @param Identifier $contentId ID to check.
	 * @return boolean
	 */
	public function hasContentWithId(Identifier $contentId): bool;

	/**
	 * Get a given Content object as a full Content object; null if not found.
	 *
	 * If the content is unpublished (or has unpublished edits), a user ID will be required to view the unpublished
	 * info.
	 *
	 * @param Identifier      $contentId ID for the content.
	 * @param Identifier|null $userId    Optional user making the query.
	 * @return Content|null
	 */
	public function contentById(Identifier $contentId, ?Identifier $userId = null): ?Content;

	/**
	 * Returns true if the given user can edit the given content.
	 *
	 * This generally means that the user is either an administrator on the Content's site or is the user on the Content
	 * object itself.
	 *
	 * @param Identifier $contentId ID for the content.
	 * @param Identifier $userId    User making the query.
	 * @return boolean
	 */
	public function userCanEditContent(Identifier $contentId, Identifier $userId): bool;
}
