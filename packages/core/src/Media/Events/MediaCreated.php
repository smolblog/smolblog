<?php

namespace Smolblog\Core\Media\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Cavatappi\Foundation\Exceptions\InvalidValueProperties;
use Cavatappi\Foundation\Factories\UuidFactory;
use Cavatappi\Foundation\Reflection\ListType;
use Cavatappi\Foundation\Reflection\MapType;
use Cavatappi\Foundation\Validation\Validated;
use Crell\Serde\Attributes\Field;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaExtension;
use Smolblog\Core\Media\Entities\MediaType;

/**
 * Indicate that a new piece of media has been uploaded and processed.
 */
class MediaCreated implements DomainEvent, Validated {
	use DomainEventKit;

	/**
	 * User responsible for the Media object.
	 *
	 * @var UuidInterface
	 */
	public readonly UuidInterface $mediaUserId;

	/**
	 * Create the event.
	 *
	 * @throws InvalidValueProperties When title or accessibilityText are empty.
	 *
	 * @param UuidInterface          $entityId          ID of the media object.
	 * @param UuidInterface          $aggregateId       ID of the site media is uploaded to.
	 * @param UuidInterface          $userId            User creating the Media.
	 * @param string                 $title             Title for the media (usually filename). Must not be empty.
	 * @param string                 $accessibilityText Text description of the media. Must not be empty.
	 * @param MediaType              $mediaType         Broad type of media (image, video, etc).
	 * @param string                 $handler           Key for handler for this media.
	 * @param array                  $fileDetails       Information needed by file handler.
	 * @param UuidInterface|null     $id                ID of the event.
	 * @param DateTimeInterface|null $timestamp         Timestamp of the event.
	 * @param UuidInterface|null     $mediaUserId       User responsible for the media; defaults to $userId.
	 * @param UuidInterface|null $processId Optional process ID.
	 * @param MediaExtension[] $extensions Any extensions added to this media.
	 */
	public function __construct(
		public readonly UuidInterface $entityId,
		public readonly UuidInterface $aggregateId,
		public readonly UuidInterface $userId,
		public readonly string $title,
		public readonly string $accessibilityText,
		public readonly MediaType $mediaType,
		public readonly string $handler,
		#[MapType('mixed')] public readonly array $fileDetails,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		?UuidInterface $mediaUserId = null,
		public readonly ?UuidInterface $processId = null,
		#[ListType(MediaExtension::class)] public array $extensions = [],
	) {
		$this->mediaUserId = $mediaUserId ?? $userId;
		$this->setIdAndTime($id, $timestamp);
		$this->validate();
	}

	/**
	 * Create an event from an existing Media object.
	 *
	 * If the user on the media object is different from the user, be sure to set mediaUserId after creation.
	 *
	 * @param Media $media Media object being created.
	 * @return self
	 */
	public static function createFromMediaObject(Media $media): self {
		return new self(
			entityId: $media->id,
			aggregateId: $media->siteId,
			userId: $media->userId,
			title: $media->title,
			accessibilityText: $media->accessibilityText,
			mediaType: $media->type,
			handler: $media->handler,
			fileDetails: $media->fileDetails,
		);
	}

	/**
	 * Get a Media object represented by this event.
	 *
	 * @return Media
	 */
	public function getMediaObject(): Media {
		return new Media(
			id: $this->entityId,
			userId: $this->mediaUserId,
			siteId: $this->aggregateId,
			title: $this->title,
			accessibilityText: $this->accessibilityText,
			type: $this->mediaType,
			handler: $this->handler,
			fileDetails: $this->fileDetails,
		);
	}

	public function validate(): void {
		if (empty($this->title) || empty($this->accessibilityText)) {
			throw new InvalidValueProperties('title and accessibilityText must not be empty.');
		}
	}
}
