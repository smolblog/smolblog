<?php

namespace Smolblog\Core\ContentV1\Media;

use Smolblog\Framework\Objects\Entity;
use Smolblog\Framework\Objects\Identifier;

/**
 * Entity for handling media uploads.
 */
class Media extends Entity {
	/**
	 * Create the Media object.
	 *
	 * @param Identifier $id                ID for this object.
	 * @param Identifier $userId            User who owns this object.
	 * @param Identifier $siteId            Site this object belongs to.
	 * @param string     $title             Title for the media (usually filename).
	 * @param string     $accessibilityText Text description of the media.
	 * @param MediaType  $type              Broad type of media (image, video, etc).
	 * @param string     $thumbnailUrl      URL for a thumbnail image.
	 * @param string     $defaultUrl        URL for a default version of the media.
	 * @param MediaFile  $file              Information about the actual file.
	 */
	public function __construct(
		Identifier $id,
		public readonly Identifier $userId,
		public readonly Identifier $siteId,
		public readonly string $title,
		public readonly string $accessibilityText,
		public readonly MediaType $type,
		public readonly string $thumbnailUrl,
		public readonly string $defaultUrl,
		public readonly MediaFile $file,
	) {
		parent::__construct($id);
	}

	/**
	 * Serialize the entity.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$base = parent::toArray();
		$base['userId'] = $this->userId->toString();
		$base['siteId'] = $this->siteId->toString();
		$base['type'] = $this->type->value;
		$base['file'] = $this->file->toArray();

		return $base;
	}

	/**
	 * Deserialize the entity.
	 *
	 * @param array $data Serialized entity.
	 * @return static
	 */
	public static function fromArray(array $data): static {
		$data['userId'] = Identifier::fromString($data['userId']);
		$data['siteId'] = Identifier::fromString($data['siteId']);
		$data['type'] = MediaType::tryFrom($data['type']);
		$data['file'] = MediaFile::fromArray($data['file']);

		return parent::fromArray($data);
	}
}
