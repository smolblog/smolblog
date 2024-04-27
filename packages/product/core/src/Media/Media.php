<?php

namespace Smolblog\Core\Media;

use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\EntityKit;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Traits\SerializableValue;
use Smolblog\Foundation\Value\Traits\SerializableValueKit;

/**
 * Entity for handling media uploads.
 */
readonly class Media extends Value implements Entity, SerializableValue {
	use EntityKit;
	use SerializableValueKit;

	/**
	 * Create the Media object.
	 *
	 * @param Identifier $id                ID for this object.
	 * @param Identifier $userId            User who owns this object.
	 * @param Identifier $siteId            Site this object belongs to.
	 * @param string     $title             Title for the media (usually filename).
	 * @param string     $accessibilityText Text description of the media.
	 * @param MediaType  $type              Broad type of media (image, video, etc).
	 * @param Identifier $fileId            ID for info about the actual file.
	 */
	public function __construct(
		Identifier $id,
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public readonly string $title,
		public readonly string $accessibilityText,
		public readonly MediaType $type,
		public readonly Identifier $fileId,
	) {
		$this->id = $id;
	}
}
