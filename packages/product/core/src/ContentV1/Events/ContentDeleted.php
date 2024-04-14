<?php

namespace Smolblog\Core\ContentV1\Events;

use DateTimeInterface;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Indicates that a piece of content has been marked as deleted.
 */
readonly class ContentDeleted extends ContentEvent {
	/**
	 * Construct the event
	 *
	 * @param Identifier             $contentId Identifier for the content this event is about.
	 * @param Identifier             $userId    User responsible for this event.
	 * @param Identifier             $siteId    Site this content belongs to.
	 * @param Identifier|null        $id        Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for this event.
	 */
	public function __construct(
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		parent::__construct(contentId: $contentId, userId: $userId, siteId: $siteId, id: $id, timestamp: $timestamp);
	}

	/**
	 * This event has no payload; returns empty array.
	 *
	 * @return array Empty array.
	 */
	public function getPayload(): array {
		return [];
	}
}
