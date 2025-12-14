<?php

namespace Smolblog\Core\Channel\Events;

use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Attributes\ArrayType;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Denotes that an asynchronous content push was successful and provides any applicable URL and/or details.
 */
readonly class ContentPushSucceeded extends DomainEvent {
	/**
	 * Create the event.
	 *
	 * @param Identifier         $contentId   The content that was pushed (in the state that it was pushed).
	 * @param Identifier         $channelId   ID of the channel being pushed to.
	 * @param Identifier         $userId      User who first initiated the action.
	 * @param Identifier         $aggregateId Site the content belongs to.
	 * @param Identifier         $processId   Identifier for this push process.
	 * @param Identifier|null    $id          Optional ID for the event.
	 * @param DateTimeField|null $timestamp   Optional timestamp for the event.
	 * @param Identifier|null    $entityId    ContentChannelEntry ID; will be created if not provided.
	 * @param Url|null           $url         Optional URL of the content on the channel.
	 * @param array              $details     Channel-specific details.
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
		public ?Url $url = null,
		#[ArrayType(ArrayType::NO_TYPE, isMap: true)] public array $details = [],
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

	/**
	 * Get the ContentChannelEntry object created by this event.
	 *
	 * @return ContentChannelEntry
	 */
	public function getEntryObject(): ContentChannelEntry {
		return new ContentChannelEntry(
			contentId: $this->contentId,
			channelId: $this->channelId,
			url: $this->url,
			details: $this->details,
		);
	}
}
