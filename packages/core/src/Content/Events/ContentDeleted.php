<?php

namespace Smolblog\Core\Content\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Content has been removed and should be purged from the system. Or at least any projections.
 */
class ContentDeleted implements DomainEvent {
	use DomainEventKit;

	/**
	 * Construct the event
	 *
	 * @param UuidInterface          $userId      ID of the user that created this event.
	 * @param UuidInterface          $aggregateId ID of the site that the content belongs to.
	 * @param UuidInterface          $entityId    ID of the content to delete.
	 * @param UuidInterface|null     $id          ID of the event.
	 * @param DateTimeInterface|null $timestamp   Timestamp of the event.
	 * @param UuidInterface|null     $processId   Optional ID of a process (series of events) this event belongs to.
	 */
	public function __construct(
		public readonly UuidInterface $userId,
		public readonly UuidInterface $aggregateId,
		public readonly UuidInterface $entityId,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		public readonly ?UuidInterface $processId = null,
	) {
		$this->setIdAndTime($id, $timestamp);
	}
}
