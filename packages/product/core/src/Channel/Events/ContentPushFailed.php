<?php

namespace Smolblog\Core\Channel\Events;

use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Foundation\Value\Attributes\ArrayType;

/**
 * Denotes that an asychronous content push has failed and provides a user-facing message and applicable details.
 */
readonly class ContentPushFailed extends DomainEvent {
/**
 * Undocumented function
 *
 * @param Identifier         $contentId   ID of the content being pushed.
 * @param Identifier         $channelId   ID of the channel being pushed to.
 * @param string             $message     User-facing message describing the failure.
 * @param Identifier         $userId      User who first initiated the action.
 * @param Identifier         $aggregateId Site the content belongs to.
 * @param Identifier         $processId   Identifier for this push process.
 * @param Identifier|null    $id          Optional ID for the event.
 * @param DateTimeField|null $timestamp   Optional timestamp for the event.
 * @param Identifier|null    $entityId    ContentChannelEntry ID; will be created if not provided.
 * @param array              $details     Channel-specific details.
 */
	public function __construct(
		public Identifier $contentId,
		public Identifier $channelId,
		public string $message,
		Identifier $userId,
		Identifier $aggregateId,
		Identifier $processId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
		?Identifier $entityId = null,
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
}
