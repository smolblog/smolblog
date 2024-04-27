<?php

namespace Smolblog\Core\Media;

use DateTimeInterface;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicate that a new piece of media has been uploaded and processed.
 */
readonly class MediaCreated extends DomainEvent {
	/**
	 * Construct the event.
	 *
	 * @param Media              $media     Media object being created.
	 * @param Identifier|null    $id        ID of the event.
	 * @param DateTimeField|null $timestamp Timestamp of the event.
	 */
	public function __construct(
		public readonly Media $media,
		?Identifier $id = null,
		?DateTimeField $timestamp = null
	) {
		parent::__construct(
			entityId: $media->id,
			userId: $media->userId,
			aggregateId: $media->siteId,
			id: $id,
			timestamp: $timestamp,
		);
	}
}
