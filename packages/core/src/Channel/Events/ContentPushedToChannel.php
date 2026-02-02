<?php

namespace Smolblog\Core\Channel\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Cavatappi\Foundation\Reflection\MapType;
use Crell\Serde\Attributes\Field;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Core\Content\Entities\Content;

/**
 * Indicates that the given Content has been pushed to the given channel.
 *
 * The full Content object is included here as a record of what was published when.
 */
class ContentPushedToChannel implements DomainEvent {
	use DomainEventKit;

	public readonly UuidInterface $entityId;

	/**
	 * Create the event.
	 *
	 * @param Content                $content     The content that was pushed (in the state that it was pushed).
	 * @param UuidInterface          $channelId   ID of the channel being pushed to.
	 * @param UuidInterface          $userId      User who first initiated the action.
	 * @param UuidInterface          $aggregateId Site the content belongs to.
	 * @param UuidInterface|null     $id          Optional ID for the event.
	 * @param DateTimeInterface|null $timestamp   Optional timestamp for the event.
	 * @param UuidInterface|null     $entityId    ContentChannelEntry ID; will be created if not provided.
	 * @param UuidInterface|null     $processId   UuidInterface for this push process if applicable.
	 * @param array                  $details     Channel-specific details.
	 */
	public function __construct(
		public readonly Content $content,
		public readonly UuidInterface $channelId,
		public readonly UuidInterface $userId,
		public readonly UuidInterface $aggregateId,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		?UuidInterface $entityId = null,
		public readonly ?UuidInterface $processId = null,
		#[MapType('mixed')] public array $details = [],
	) {
		$this->entityId = $entityId ?? ContentChannelEntry::buildId(contentId: $content->id, channelId: $channelId);
		$this->setIdAndTime($id, $timestamp);
	}
}
