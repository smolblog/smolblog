<?php

namespace Smolblog\Core\Media\Data;

use Smolblog\Core\Media\Entities\Media;
use Smolblog\Foundation\Value\Fields\Identifier;

interface MediaRepo {
	/**
	 * Check if a given media object exists.
	 *
	 * @param Identifier $mediaId ID to check.
	 * @return boolean
	 */
	public function hasMediaWithId(Identifier $mediaId): bool;

	/**
	 * Get the specified Media object.
	 *
	 * @param Identifier $mediaId Media to fetch.
	 * @return Media|null
	 */
	public function mediaById(Identifier $mediaId): ?Media;
}
