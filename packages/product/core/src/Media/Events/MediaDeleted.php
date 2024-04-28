<?php

namespace Smolblog\Core\Media\Events;

use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicate that attributes have been changed on a piece of media.
 */
readonly class MediaDeleted extends DomainEvent {
	/**
	 * Construct the event.
	 *
	 * @param Identifier         $entityId    ID of the Media object.
	 * @param Identifier         $userId      User deleting the media.
	 * @param Identifier         $aggregateId Site media is being deleting from.
	 * @param Identifier|null    $id          ID of the event.
	 * @param DateTimeField|null $timestamp   Timestamp of the event.
	 */
	public function __construct(
		Identifier $entityId,
		Identifier $userId,
		Identifier $aggregateId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null
	) {
		parent::__construct(
			entityId: $entityId,
			userId: $userId,
			aggregateId: $aggregateId,
			id: $id ?? new DateIdentifier(),
			timestamp: $timestamp ?? new DateTimeField(),
		);
	}
}
