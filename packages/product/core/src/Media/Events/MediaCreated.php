<?php

namespace Smolblog\Core\Media;

use DateTimeInterface;
use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicate that a new piece of media has been uploaded and processed.
 */
readonly class MediaCreated extends DomainEvent {
	/**
	 * Create the event.
	 *
	 * @param Media              $media       Media object being created.
	 * @param Identifier         $userId      User creating the Media.
	 * @param Identifier|null    $entityId    ID of the media; inferred from $media.
	 * @param Identifier|null    $aggregateId ID of the site the Media belongs to; inferred from $media.
	 * @param Identifier|null    $id          ID of the event.
	 * @param DateTimeField|null $timestamp   Timestamp of the event.
	 */
	public function __construct(
		public Media $media,
		Identifier $userId,
		?Identifier $entityId = null,
		?Identifier $aggregateId = null,
		?Identifier $id = null,
		?DateTimeField $timestamp = null
	) {
		parent::__construct(
			id: $id ?? new DateIdentifier(),
			timestamp: $timestamp ?? new DateTimeField(),
			userId: $userId,
			aggregateId: $aggregateId ?? $this->media->siteId,
			entityId: $entityId ?? $this->media->id,
		);
	}
}
