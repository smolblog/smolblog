<?php

namespace Smolblog\Core\Channel\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Cavatappi\Foundation\Factories\UuidFactory;
use DateTimeImmutable;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Indicates that a Channel has been linked to a Site.
 */
class ChannelAddedToSite implements DomainEvent {
	use DomainEventKit;

	/**
	 * Create the event.
	 *
	 * @param UuidInterface          $aggregateId ID of the Site being linked to.
	 * @param UuidInterface          $entityId    ID of the Channel being linked.
	 * @param UuidInterface          $userId      ID of the user initiating this change.
	 * @param UuidInterface|null     $processId   Optional ID for the overall process.
	 * @param UuidInterface|null     $id          Optional ID for the event.
	 * @param DateTimeInterface|null $timestamp   Optional timestamp for the event (default now).
	 */
	public function __construct(
		public readonly UuidInterface $aggregateId,
		public readonly UuidInterface $entityId,
		public readonly UuidInterface $userId,
		public readonly ?UuidInterface $processId = null,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
	) {
		$this->setIdAndTime($id, $timestamp);
	}
}
