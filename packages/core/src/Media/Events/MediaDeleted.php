<?php

namespace Smolblog\Core\Media\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Indicate that attributes have been changed on a piece of media.
 */
class MediaDeleted implements DomainEvent {
	use DomainEventKit;

	/**
	 * Construct the event.
	 *
	 * @param UuidInterface          $entityId    ID of the Media object.
	 * @param UuidInterface          $userId      User deleting the media.
	 * @param UuidInterface          $aggregateId Site media is being deleting from.
	 * @param UuidInterface|null     $id          ID of the event.
	 * @param DateTimeInterface|null $timestamp   Timestamp of the event.
	 */
	public function __construct(
		public readonly UuidInterface $entityId,
		public readonly UuidInterface $userId,
		public readonly UuidInterface $aggregateId,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		public readonly ?UuidInterface $processId = null,
	) {
		$this->setIdAndTime($id, $timestamp);
	}
}
