<?php

namespace Smolblog\Core\Media\Data;

use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Media\Entities\Media;

interface MediaRepo {
	/**
	 * Check if a given media object exists.
	 *
	 * @param UuidInterface $mediaId ID to check.
	 * @return boolean
	 */
	public function hasMediaWithId(UuidInterface $mediaId): bool;

	/**
	 * Get the specified Media object.
	 *
	 * @param UuidInterface $mediaId Media to fetch.
	 * @return Media|null
	 */
	public function mediaById(UuidInterface $mediaId): ?Media;
}
