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
	 * @param string             $channelKey   Key of the channel being deleted.
	 * @param Identifier         $connectionId ID of the connection this event belongs to.
	 * @param Identifier         $userId       ID of the user initiating this change.
	 * @param Identifier|null    $id           Optional ID for the event.
	 * @param DateTimeField|null $timestamp    Optional timestamp for the event (default now).
	 */
	public function __construct(
		public readonly string $channelKey,
		Identifier $connectionId,
		Identifier $userId,
		Identifier $id = null,
		DateTimeField $timestamp = null,
	) {
		parent::__construct(connectionId: $connectionId, userId: $userId, id: $id, timestamp: $timestamp);
	}
}
