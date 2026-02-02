<?php

namespace Smolblog\Core\Connection\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Crell\Serde\Attributes\Field;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Indicates that a given connection should be deleted and its associated artifacts removed.
 */
class ConnectionDeleted implements DomainEvent {
	use DomainEventKit;

	/**
	 * Create the Event
	 *
	 * @param UuidInterface          $entityId  ID of the connection this event belongs to.
	 * @param UuidInterface          $userId    ID of the user initiating this change.
	 * @param UuidInterface|null     $processId Optional ID for the process causing the event.
	 * @param UuidInterface|null     $id        Optional ID for the event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for the event (default now).
	 */
	public function __construct(
		public readonly UuidInterface $entityId,
		public readonly UuidInterface $userId,
		public readonly ?UuidInterface $processId = null,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
	) {
		$this->setIdAndTime($id, $timestamp);
	}

	/**
	 * Aggregate ID is not used
	 *
	 * @var null
	 */
	#[Field(exclude: true)]
	public null $aggregateId { get => null; }
}
