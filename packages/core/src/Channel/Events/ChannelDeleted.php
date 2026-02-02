<?php

namespace Smolblog\Core\Channel\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Crell\Serde\Attributes\Field;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Indicates a Channel is no longer active and has been deleted.
 */
class ChannelDeleted implements DomainEvent {
	use DomainEventKit;

	/**
	 * Construct the event.
	 *
	 * @param UuidInterface          $entityId  ID of the channel being deleted.
	 * @param UuidInterface          $userId    ID of the user initiating this change.
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

	#[Field(exclude: true)]
	public null $aggregateId { get => null; }
}
