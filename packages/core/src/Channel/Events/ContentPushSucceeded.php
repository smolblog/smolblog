<?php

namespace Smolblog\Core\Channel\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Cavatappi\Foundation\Reflection\MapType;
use DateTimeInterface;
use Psr\Http\Message\UriInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Channel\Entities\ContentChannelEntry;

/**
 * Denotes that an asynchronous content push was successful and provides any applicable URL and/or details.
 */
class ContentPushSucceeded implements DomainEvent {
	use DomainEventKit;

	public readonly UuidInterface $entityId;

	/**
	 * Create the event.
	 *
	 * @param UuidInterface          $contentId   The content that was pushed (in the state that it was pushed).
	 * @param UuidInterface          $channelId   ID of the channel being pushed to.
	 * @param UuidInterface          $userId      User who first initiated the action.
	 * @param UuidInterface          $aggregateId Site the content belongs to.
	 * @param UuidInterface          $processId   UuidInterface for this push process.
	 * @param UuidInterface|null     $id          Optional ID for the event.
	 * @param DateTimeInterface|null $timestamp   Optional timestamp for the event.
	 * @param UuidInterface|null     $entityId    ContentChannelEntry ID; will be created if not provided.
	 * @param UriInterface|null      $url         Optional URL of the content on the channel.
	 * @param array                  $details     Channel-specific details.
	 */
	public function __construct(
		public readonly UuidInterface $contentId,
		public readonly UuidInterface $channelId,
		public readonly UuidInterface $userId,
		public readonly UuidInterface $aggregateId,
		public readonly UuidInterface $processId,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		?UuidInterface $entityId = null,
		public readonly ?UriInterface $url = null,
		#[MapType('mixed')] public array $details = [],
	) {
		$this->entityId = $entityId ?? ContentChannelEntry::buildId(
			contentId: $this->contentId,
			channelId: $this->channelId,
		);
		$this->setIdAndTime($id, $timestamp);
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
