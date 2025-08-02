<?php

namespace Smolblog\Core\Media\Entities;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\EntityKit;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Traits\ArrayType;
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
	 * @throws InvalidValueProperties When Title or A11y text are empty.
	 *
	 * @param Identifier $id                ID for this object.
	 * @param Identifier $userId            User who owns this object.
	 * @param Identifier $siteId            Site this object belongs to.
	 * @param string     $title             Title for the media (usually filename). Must not be empty.
	 * @param string     $accessibilityText Text description of the media. Must not be empty.
	 * @param MediaType  $type              Broad type of media (image, video, etc).
	 * @param string     $handler           Key for handler for this media.
	 * @param array      $fileDetails       Information needed by file handler.
	 */
	public function __construct(
		Identifier $id,
		public Identifier $userId,
		public Identifier $siteId,
		public string $title,
		public string $accessibilityText,
		public MediaType $type,
		public string $handler,
		#[ArrayType(ArrayType::NO_TYPE, isMap: true)] public array $fileDetails,
	) {
		if (empty($this->title) || empty($this->accessibilityText)) {
			throw new InvalidValueProperties('title and accessibilityText must not be empty.');
		}

		$this->id = $id;
	}
}
