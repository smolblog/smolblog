<?php

namespace Smolblog\Core\Connection\Events;

use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicates that a given connection should be deleted and its associated artifacts removed.
 */
readonly class ConnectionDeleted extends DomainEvent {
	/**
	 * Create the Event
	 *
	 * @param Identifier         $entityId  ID of the connection this event belongs to.
	 * @param Identifier         $userId    ID of the user initiating this change.
	 * @param Identifier|null    $id        Optional ID for the event.
	 * @param DateTimeField|null $timestamp Optional timestamp for the event (default now).
	 */
	public function __construct(
		Identifier $entityId,
		Identifier $userId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
	) {
		parent::__construct(entityId: $entityId, userId: $userId, id: $id, timestamp: $timestamp);
	}
}
