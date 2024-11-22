<?php

namespace Smolblog\Core\Channel\Events;

use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicates a Channel has been created.
 */
readonly class ChannelSaved extends DomainEvent {
	/**
	 * Create the event.
	 *
	 * @param Channel            $channel   Channel object being saved.
	 * @param Identifier         $userId    User creating the channel.
	 * @param Identifier|null    $entityId  Channel ID; will be auto-generated.
	 * @param Identifier|null    $id        Optional ID for the event.
	 * @param DateTimeField|null $timestamp Optional timestamp for the event.
	 */
	public function __construct(
		public Channel $channel,
		Identifier $userId,
		?Identifier $entityId = null,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
	) {
		parent::__construct(
			entityId: $entityId ?? $this->channel->getId(),
			userId: $userId,
			id: $id,
			timestamp: $timestamp
		);
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
