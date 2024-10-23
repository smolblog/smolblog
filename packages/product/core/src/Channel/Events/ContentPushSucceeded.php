<?php

namespace Smolblog\Core\Channel\Events;

use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Denotes that a content push was successful and provides any applicable URL and/or details.
 */
readonly class ContentPushSucceeded extends DomainEvent {
	/**
	 * Create the event.
	 *
	 * @param Identifier         $contentId    ID of the content being pushed.
	 * @param Identifier         $channelId    ID of the channel being pushed to.
	 * @param Identifier         $startEventId ID of the ContentPushStarted event this event is completing.
	 * @param Identifier         $userId       User who first initiated the action.
	 * @param Identifier         $aggregateId  Site the content belongs to.
	 * @param Identifier|null    $id           Optional ID for the event.
	 * @param DateTimeField|null $timestamp    Optional timestamp for the event.
	 * @param Identifier|null    $entityId     ContentChannelEntry ID; will be created if not provided.
	 * @param string|null        $url          Optional URL of the content on the channel.
	 * @param array              $details      Channel-specific details.
	 */
	public function __construct(
		public Identifier $contentId,
		public Identifier $channelId,
		public Identifier $startEventId,
		Identifier $userId,
		Identifier $aggregateId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
		?Identifier $entityId = null,
		public ?string $url = null,
		public array $details = [],
	) {
		parent::__construct(
			userId: $userId,
			id: $id,
			timestamp: $timestamp,
			aggregateId: $aggregateId,
			entityId: $entityId ?? ContentChannelEntry::buildId(contentId: $contentId, channelId: $channelId),
		);
	}
}
