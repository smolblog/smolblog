<?php

namespace Smolblog\Core\Connection\Events;

use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicates a Connection has been formed or re-formed between a user account and an external handler.
 */
readonly class ConnectionRefreshed extends DomainEvent {
	/**
	 * Create the Event
	 *
	 * @param array              $details   Updated information needed to connect to this handler.
	 * @param Identifier         $entityId  ID of the connection this event belongs to.
	 * @param Identifier         $userId    ID of the user initiating this change.
	 * @param Identifier|null    $id        Optional ID for the event.
	 * @param DateTimeField|null $timestamp Optional timestamp for the event (default now).
	 */
	public function __construct(
		public readonly array $details,
		Identifier $entityId,
		Identifier $userId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
	) {
		parent::__construct(entityId: $entityId, userId: $userId, id: $id, timestamp: $timestamp);
	}
}
