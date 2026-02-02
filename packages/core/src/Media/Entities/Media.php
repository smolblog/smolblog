<?php

namespace Smolblog\Core\Media\Entities;

use Cavatappi\Foundation\DomainEvent\Entity;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Reflection\MapType;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

/**
 * Entity for handling media uploads.
 */
readonly class Media implements Entity, Value, Validated {
	use ValueKit;

	/**
	 * Create the Media object.
	 *
	 * @throws InvalidValueProperties When Title or A11y text are empty.
	 *
	 * @param UuidInterface $id                ID for this object.
	 * @param UuidInterface $userId            User who owns this object.
	 * @param UuidInterface $siteId            Site this object belongs to.
	 * @param string        $title             Title for the media (usually filename). Must not be empty.
	 * @param string        $accessibilityText Text description of the media. Must not be empty.
	 * @param MediaType     $type              Broad type of media (image, video, etc).
	 * @param string        $handler           Key for handler for this media.
	 * @param array         $fileDetails       Information needed by file handler.
	 */
	public function __construct(
		public UuidInterface $id,
		public UuidInterface $userId,
		public UuidInterface $siteId,
		public string $title,
		public string $accessibilityText,
		public MediaType $type,
		public string $handler,
		#[MapType('mixed')] public array $fileDetails,
	) {
		$this->validate();
	}

	public function validate(): void {
		if (empty($this->title) || empty($this->accessibilityText)) {
			throw new InvalidValueProperties('title and accessibilityText must not be empty.');
		}
	}
}
