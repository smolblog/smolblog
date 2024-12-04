<?php

namespace Smolblog\Core\Channel\Events;

use Smolblog\Foundation\Value\Fields\{DateTimeField, Identifier};
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicates a Channel is no longer active and has been deleted.
 */
readonly class ChannelDeleted extends DomainEvent {
	/**
	 * Construct the event.
	 *
	 * @param Identifier         $entityId  ID of the channel being deleted.
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

	/**
	 * Remove 'aggregateId' from (de)serialization.
	 *
	 * @return array
	 */
	protected static function propertyInfo(): array {
		$base = parent::propertyInfo();
		unset($base['aggregateId']);
		return $base;
	}
}
