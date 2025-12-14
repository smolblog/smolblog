<?php

namespace Smolblog\Core\Content\Events;

use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Content has been removed and should be purged from the system. Or at least any projections.
 */
readonly class ContentDeleted extends DomainEvent {
	/**
	 * Construct the event
	 *
	 * @param Identifier         $userId      ID of the user that created this event.
	 * @param Identifier         $aggregateId ID of the site that the content belongs to.
	 * @param Identifier         $entityId    ID of the content to delete.
	 * @param Identifier|null    $id          ID of the event.
	 * @param DateTimeField|null $timestamp   Timestamp of the event.
	 * @param Identifier|null    $processId   Optional ID of a process (series of events) this event belongs to.
	 */
	public function __construct(
		Identifier $userId,
		Identifier $aggregateId,
		Identifier $entityId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
		?Identifier $processId = null,
	) {
		parent::__construct(
			userId: $userId,
			id: $id,
			timestamp: $timestamp,
			aggregateId: $aggregateId,
			entityId: $entityId,
			processId: $processId,
		);
	}
}
