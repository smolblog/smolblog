<?php

namespace Smolblog\Core\Content\Events;

use DateTimeInterface;
use Smolblog\Core\Content\ContentVisibility;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates that a piece of content has changed visibility.
 *
 * I.E. "a thing was published" or "a thing was un-published." This is a significant event, particularly as it
 * determines when something moves from "not public" to "public" or vice-versa.
 */
class ContentVisibilityChanged extends ContentEvent {
	/**
	 * Construct the event
	 *
	 * @param ContentVisibility      $visibility Updated visibility of the content.
	 * @param Identifier             $contentId  Identifier for the content this event is about.
	 * @param Identifier             $userId     User responsible for this event.
	 * @param Identifier             $siteId     Site this content belongs to.
	 * @param Identifier|null        $id         Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp  Optional timestamp for this event.
	 */
	public function __construct(
		public readonly ContentVisibility $visibility,
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		parent::__construct(contentId: $contentId, userId: $userId, siteId: $siteId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Get properties unique to this event.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [
			'visibility' => strval($this->visibility),
		];
	}

	/**
	 * Deserialize this event's payload.
	 *
	 * @param array $payload Serialized payload.
	 * @return array
	 */
	protected static function payloadFromArray(array $payload): array {
		return [
			'visibility' => ContentVisibility::from($payload['visibility'])
		];
	}
}
