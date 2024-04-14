<?php

namespace Smolblog\Core\Connector\Events;

use DateTimeInterface;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Indicates a Channel has been created from a Connection.
 */
readonly class ChannelSaved extends ConnectorEvent {
	/**
	 * Create the event.
	 *
	 * @param string                 $channelKey   Key that uniquely identifies the channel with its Connection.
	 * @param string                 $displayName  Human-readable name to identify the Channel.
	 * @param array                  $details      Any additional information needed to interact with the Channel.
	 * @param Identifier             $connectionId ID of the connection this event belongs to.
	 * @param Identifier             $userId       ID of the user initiating this change.
	 * @param Identifier|null        $id           Optional ID for the event.
	 * @param DateTimeInterface|null $timestamp    Optional timestamp for the event (default now).
	 */
	public function __construct(
		public readonly string $channelKey,
		public readonly string $displayName,
		public readonly array $details,
		Identifier $connectionId,
		Identifier $userId,
		Identifier $id = null,
		DateTimeInterface $timestamp = null,
	) {
		parent::__construct(connectionId: $connectionId, userId: $userId, id: $id, timestamp: $timestamp);
	}

	/**
	 * For subclasses to provide any additional fields in a serialized array format.
	 *
	 * In this format so that the additional fields can be type-checked by the subclasses but still serialized and
	 * stored in a standard format.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [
			'channelKey' => $this->channelKey,
			'displayName' => $this->displayName,
			'details' => $this->details,
		];
	}
}
