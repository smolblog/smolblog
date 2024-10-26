<?php

namespace Smolblog\Core\Channel\Events;

use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Denotes that a piece of content has been marked for publication on a channel.
 */
readonly class ContentPushStarted extends DomainEvent {
	/**
	 * Create the event.
	 *
	 * @param Identifier         $contentId   ID of the content being pushed.
	 * @param Identifier         $channelId   ID of the channel being pushed to.
	 * @param Identifier         $userId      User initiating the action.
	 * @param Identifier         $aggregateId Site the content belongs to.
	 * @param Identifier         $processId   Identifier for this push process.
	 * @param Identifier|null    $id          Optional ID for the event.
	 * @param DateTimeField|null $timestamp   Optional timestamp for the event.
	 * @param Identifier|null    $entityId    ContentChannelEntry ID; will be created if not provided.
	 */
	public function __construct(
		public Identifier $contentId,
		public Identifier $channelId,
		Identifier $userId,
		Identifier $aggregateId,
		Identifier $processId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
		?Identifier $entityId = null,
	) {
		parent::__construct(
			userId: $userId,
			id: $id,
			timestamp: $timestamp,
			aggregateId: $aggregateId,
			entityId: $entityId ?? ContentChannelEntry::buildId(contentId: $contentId, channelId: $channelId),
			processId: $processId,
		);
	}
}
