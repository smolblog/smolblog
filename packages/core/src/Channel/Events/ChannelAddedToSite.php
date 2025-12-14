<?php

namespace Smolblog\Core\Channel\Events;

use Smolblog\Foundation\Value\Fields\{Identifier, DateTimeField};
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicates that a Channel has been linked to a Site.
 */
readonly class ChannelAddedToSite extends DomainEvent {
	/**
	 * Create the event.
	 *
	 * @param Identifier         $aggregateId ID of the Site being linked to.
	 * @param Identifier         $entityId    ID of the Channel being linked.
	 * @param Identifier         $userId      ID of the user initiating this change.
	 * @param Identifier|null    $id          Optional ID for the event.
	 * @param DateTimeField|null $timestamp   Optional timestamp for the event (default now).
	 */
	public function __construct(
		Identifier $aggregateId,
		Identifier $entityId,
		Identifier $userId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
	) {
		parent::__construct(
			entityId: $entityId,
			aggregateId: $aggregateId,
			userId: $userId,
			id: $id,
			timestamp: $timestamp,
		);
	}
}
