<?php

namespace Smolblog\Core\ContentV1\Events;

use DateTimeInterface;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Indicates a permalink has been assigned to a piece of content.
 *
 * At least right now, permalinks are handled by the external system. Rather than try to anticipate what the permalink
 * structure is or will be, the external system can fire this event when the permalink is assigned (usually on content
 * publish). This will allow Smolblog's projections to remain in sync with the outside system.
 */
class PermalinkAssigned extends ContentEvent {
	/**
	 * Create the event
	 *
	 * @param Identifier             $contentId Identifier for the content this event is about.
	 * @param Identifier             $userId    User responsible for this event.
	 * @param Identifier             $siteId    Site this content belongs to.
	 * @param string                 $permalink Newly-assigned permalink for the given content.
	 * @param Identifier|null        $id        Optional identifier for this event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for this event.
	 */
	public function __construct(
		Identifier $contentId,
		Identifier $userId,
		Identifier $siteId,
		public readonly string $permalink,
		?Identifier $id = null,
		?DateTimeInterface $timestamp = null
	) {
		parent::__construct(contentId: $contentId, userId: $userId, siteId: $siteId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Get the payload for this event.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return ['permalink' => $this->permalink];
	}
}
