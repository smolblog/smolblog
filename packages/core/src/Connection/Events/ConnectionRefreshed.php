<?php

namespace Smolblog\Core\Connection\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Cavatappi\Foundation\Reflection\MapType;
use Crell\Serde\Attributes\Field;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Indicates a Connection has been formed or re-formed between a user account and an external handler.
 */
class ConnectionRefreshed implements DomainEvent {
	use DomainEventKit;

	/**
	 * Create the Event
	 *
	 * @param array                  $details   Updated information needed to connect to this handler.
	 * @param UuidInterface          $entityId  ID of the connection this event belongs to.
	 * @param UuidInterface          $userId    ID of the user initiating this change.
	 * @param UuidInterface|null     $processId Optional ID for the event.
	 * @param UuidInterface|null     $id        Optional ID for the event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for the event (default now).
	 */
	public function __construct(
		#[MapType('mixed')] public readonly array $details,
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
