<?php

namespace Smolblog\Core\Connector\Events;

use DateTimeInterface;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Indicates that a given connection should be deleted and its associated artifacts removed.
 */
readonly class ConnectionDeleted extends ConnectorEvent {
	/**
	 * Create the Event
	 *
	 * @param Identifier             $connectionId ID of the connection this event belongs to.
	 * @param Identifier             $userId       ID of the user initiating this change.
	 * @param Identifier|null        $id           Optional ID for the event.
	 * @param DateTimeInterface|null $timestamp    Optional timestamp for the event (default now).
	 */
	public function __construct(
		Identifier $connectionId,
		Identifier $userId,
		Identifier $id = null,
		DateTimeInterface $timestamp = null,
	) {
		parent::__construct(connectionId: $connectionId, userId: $userId, id: $id, timestamp: $timestamp);
	}

	/**
	 * This event has no payload.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [];
	}
}
