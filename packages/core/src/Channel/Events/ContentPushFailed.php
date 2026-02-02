<?php

namespace Smolblog\Core\Channel\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Cavatappi\Foundation\Reflection\MapType;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Channel\Entities\ContentChannelEntry;

/**
 * Denotes that an asychronous content push has failed and provides a user-facing message and applicable details.
 */
class ContentPushFailed implements DomainEvent {
	use DomainEventKit;

	public readonly UuidInterface $entityId;

	/**
	 * Undocumented function
	 *
	 * @param UuidInterface          $contentId   ID of the content being pushed.
	 * @param UuidInterface          $channelId   ID of the channel being pushed to.
	 * @param string                 $message     User-facing message describing the failure.
	 * @param UuidInterface          $userId      User who first initiated the action.
	 * @param UuidInterface          $aggregateId Site the content belongs to.
	 * @param UuidInterface          $processId   UuidInterface for this push process.
	 * @param UuidInterface|null     $id          Optional ID for the event.
	 * @param DateTimeInterface|null $timestamp   Optional timestamp for the event.
	 * @param UuidInterface|null     $entityId    ContentChannelEntry ID; will be created if not provided.
	 * @param array                  $details     Channel-specific details.
	 */
	public function __construct(
		public readonly UuidInterface $contentId,
		public readonly UuidInterface $channelId,
		public readonly string $message,
		public readonly UuidInterface $userId,
		public readonly UuidInterface $aggregateId,
		public readonly UuidInterface $processId,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		?UuidInterface $entityId = null,
		#[MapType('mixed')] public array $details = [],
	) {
		$this->entityId = $entityId ?? ContentChannelEntry::buildId(contentId: $this->contentId, channelId: $this->channelId);
		$this->setIdAndTime($id, $timestamp);
	}
}
