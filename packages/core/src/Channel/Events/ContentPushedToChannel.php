<?php

namespace Smolblog\Core\Channel\Events;

use Smolblog\Core\Channel\Entities\ContentChannelEntry;
use Smolblog\Core\Content\Entities\Content;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\Url;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Foundation\Value\Attributes\ArrayType;

/**
 * Indicates that the given Content has been pushed to the given channel.
 *
 * The full Content object is included here as a record of what was published when.
 */
readonly class ContentPushedToChannel extends DomainEvent {
	/**
	 * Create the event.
	 *
	 * @param Content            $content     The content that was pushed (in the state that it was pushed).
	 * @param Identifier         $channelId   ID of the channel being pushed to.
	 * @param Identifier         $userId      User who first initiated the action.
	 * @param Identifier         $aggregateId Site the content belongs to.
	 * @param Identifier|null    $id          Optional ID for the event.
	 * @param DateTimeField|null $timestamp   Optional timestamp for the event.
	 * @param Identifier|null    $entityId    ContentChannelEntry ID; will be created if not provided.
	 * @param Identifier|null    $processId   Identifier for this push process if applicable.
	 * @param array              $details     Channel-specific details.
	 */
	public function __construct(
		public Content $content,
		public Identifier $channelId,
		Identifier $userId,
		Identifier $aggregateId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
		?Identifier $entityId = null,
		?Identifier $processId = null,
		#[ArrayType(ArrayType::NO_TYPE, isMap: true)] public array $details = [],
	) {
		parent::__construct(
			userId: $userId,
			id: $id,
			timestamp: $timestamp,
			aggregateId: $aggregateId,
			entityId: $entityId ?? ContentChannelEntry::buildId(contentId: $content->id, channelId: $channelId),
			processId: $processId,
		);
	}
}
