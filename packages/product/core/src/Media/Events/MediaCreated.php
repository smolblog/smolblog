<?php

namespace Smolblog\Core\Media\Events;

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
	 * @param Media              $media     Media object being created.
	 * @param Identifier         $userId    User creating the Media.
	 * @param Identifier|null    $id        ID of the event.
	 * @param DateTimeField|null $timestamp Timestamp of the event.
	 */
	public function __construct(
		public Media $media,
		Identifier $userId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null
	) {
		parent::__construct(
			id: $id ?? new DateIdentifier(),
			timestamp: $timestamp ?? new DateTimeField(),
			userId: $userId,
			aggregateId: $this->media->siteId,
			entityId: $this->media->id,
		);
	}

	/**
	 * Create event from serialized data.
	 *
	 * Removes the 'aggreagateId' and 'entityId' properties as they are taken from the Media object.
	 *
	 * @param array $data Serialized data.
	 * @return static
	 */
	public static function deserializeValue(array $data): static {
		unset($data['aggregateId'], $data['entityId']);
		return parent::deserializeValue($data);
	}
}
