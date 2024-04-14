<?php

namespace Smolblog\Core\Connector\Events;

use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Base event for Connector-related events.
 */
abstract readonly class ConnectorEvent extends DomainEvent {
	/**
	 * Create the Event
	 *
	 * @param Identifier             $connectionId ID of the connection this event belongs to.
	 * @param Identifier             $userId       ID of the user initiating this change.
	 * @param Identifier|null        $id           Optional ID for the event.
	 * @param DateTimeField|null $timestamp    Optional timestamp for the event (default now).
	 */
	public function __construct(
		Identifier $connectionId,
		Identifier $userId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
		?Identifier $siteId = null,
	) {
		parent::__construct(
			id: $id,
			timestamp: $timestamp,
			userId: $userId,
			aggregateId: $siteId,
			entityId: $connectionId,
		);
	}
}
