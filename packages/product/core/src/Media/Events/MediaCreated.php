<?php

namespace Smolblog\Core\Media\Events;

use Smolblog\Core\Media\Entities\Media;
use Smolblog\Core\Media\Entities\MediaType;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicate that a new piece of media has been uploaded and processed.
 */
readonly class MediaCreated extends DomainEvent {
	/**
	 * User responsible for the Media object.
	 *
	 * @var Identifier
	 */
	public Identifier $mediaUserId;

	/**
	 * Create the event.
	 *
	 * @throws InvalidValueProperties When title or accessibilityText are empty.
	 *
	 * @param Identifier         $entityId          ID of the media object.
	 * @param Identifier         $aggregateId       ID of the site media is uploaded to.
	 * @param Identifier         $userId            User creating the Media.
	 * @param string             $title             Title for the media (usually filename). Must not be empty.
	 * @param string             $accessibilityText Text description of the media. Must not be empty.
	 * @param MediaType          $mediaType         Broad type of media (image, video, etc).
	 * @param string             $handler           Key for handler for this media.
	 * @param array              $fileDetails       Information needed by file handler.
	 * @param Identifier|null    $id                ID of the event.
	 * @param DateTimeField|null $timestamp         Timestamp of the event.
	 * @param Identifier|null    $mediaUserId       User responsible for the media; defaults to $userId.
	 */
	public function __construct(
		Identifier $entityId,
		Identifier $aggregateId,
		Identifier $userId,
		public string $title,
		public string $accessibilityText,
		public MediaType $mediaType,
		public string $handler,
		public array $fileDetails,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
		?Identifier $mediaUserId = null,
	) {
		if (empty($this->title) || empty($this->accessibilityText)) {
			throw new InvalidValueProperties('title and accessibilityText must not be empty.');
		}

		$this->mediaUserId = $mediaUserId ?? $userId;

		parent::__construct(
			id: $id,
			timestamp: $timestamp,
			userId: $userId,
			aggregateId: $aggregateId,
			entityId: $entityId,
		);
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
			id: $this->entityId ?? Identifier::nil(),
			userId: $this->mediaUserId,
			siteId: $this->aggregateId ?? Identifier::nil(),
			title: $this->title,
			accessibilityText: $this->accessibilityText,
			type: $this->mediaType,
			handler: $this->handler,
			fileDetails: $this->fileDetails,
		);
	}
}
