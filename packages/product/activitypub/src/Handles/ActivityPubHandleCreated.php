<?php

namespace Smolblog\ActivityPub\Handles;

use DateTimeInterface;
use Smolblog\Core\Site\SiteEvent;
use Smolblog\Framework\Objects\Identifier;

/**
 * Indicates that a new ActivityPub handle has been created.
 */
class ActivityPubHandleCreated extends SiteEvent {
	/**
	 * Create the event.
	 *
	 * @param Identifier             $handleId  ID of the handle.
	 * @param string                 $handle    ActivityPub handle.
	 * @param Identifier             $siteId    ID of the site this handle belongs to.
	 * @param Identifier             $userId    ID of the user creating the handle.
	 * @param Identifier|null        $id        ID for this event.
	 * @param DateTimeInterface|null $timestamp Time of the event.
	 */
	public function __construct(
		public readonly Identifier $handleId,
		public readonly string $handle,
		Identifier $siteId,
		Identifier $userId,
		Identifier $id = null,
		DateTimeInterface $timestamp = null,
	) {
		parent::__construct(siteId: $siteId, userId: $userId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Get the payload for this event.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [
			'handleId' => $this->handleId->toString(),
			'handle' => $this->handle,
		];
	}
}
