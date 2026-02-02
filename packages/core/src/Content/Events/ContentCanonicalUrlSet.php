<?php

namespace Smolblog\Core\Content\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use DateTimeInterface;
use Psr\Http\Message\UriInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Indicates that a canonical URL has been set for this content.
 *
 * This should usually be a personal website or other user-controlled page. Once the content is pushed to that channel,
 * the handler should dispatch this event to update the Content state. Other channels may use this to post links back
 * to the original/canonical store (for example, a Note may be posted in full while a longer Article is only linked to
 * from an external channel).
 */
class ContentCanonicalUrlSet implements DomainEvent {
	use DomainEventKit;

	/**
	 * Create the event.
	 *
	 * @param UriInterface           $url         New canonical URL for the Content.
	 * @param UuidInterface          $aggregateId Site the content belongs to.
	 * @param UuidInterface          $userId      User making the change.
	 * @param UuidInterface          $entityId    ID of the content object.
	 * @param UuidInterface|null     $id          ID for this event.
	 * @param DateTimeInterface|null $timestamp   Timestamp for this event.
	 * @param UuidInterface|null     $processId   Optional process ID this belongs to.
	 */
	public function __construct(
		public readonly UriInterface $url,
		public readonly UuidInterface $aggregateId,
		public readonly UuidInterface $userId,
		public readonly UuidInterface $entityId,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		public readonly ?UuidInterface $processId = null,
	) {
		$this->setIdAndTime($id, $timestamp);
	}
}
