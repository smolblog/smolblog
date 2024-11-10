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
	 * @param Identifier $contentId ID for the content.
	 * @return Content|null
	 */
	public function contentById(Identifier $contentId): ?Content;
}
