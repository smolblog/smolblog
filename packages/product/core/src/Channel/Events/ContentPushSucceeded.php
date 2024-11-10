<?php

namespace Smolblog\Core\Channel\Events;

use Smolblog\Core\Content\Entities\Content;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Fields\Url;

/**
 * Denotes that an asynchronous content push was successful and provides any applicable URL and/or details.
 */
readonly class ContentPushSucceeded extends ContentPushedToChannel {
	/**
	 * Create the event.
	 *
	 * @param Content            $content     The content that was pushed (in the state that it was pushed).
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
		Content $content,
		Identifier $channelId,
		Identifier $userId,
		Identifier $aggregateId,
		Identifier $processId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
		?Identifier $entityId = null,
		?Url $url = null,
		array $details = [],
	) {
		parent::__construct(
			content: $content,
			channelId: $channelId,
			userId: $userId,
			id: $id,
			timestamp: $timestamp,
			aggregateId: $aggregateId,
			entityId: $entityId,
			processId: $processId,
			url: $url,
			details: $details,
		);
	}
}
