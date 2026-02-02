<?php

namespace Smolblog\Core\Connection\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Cavatappi\Foundation\Reflection\MapType;
use Crell\Serde\Attributes\Field;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Connection\Entities\Connection;

/**
 * Indicates a Connection has been formed or re-formed between a user account and an external handler.
 */
class ConnectionEstablished implements DomainEvent {
	use DomainEventKit;

	/**
	 * Create the Event
	 *
	 * @param string                 $handler     Key for the handler this connection is for.
	 * @param string                 $handlerKey  Unique identifier for this connection for this handler.
	 * @param string                 $displayName Human-readable name for this connection.
	 * @param array                  $details     Additional information needed to connect to this handler.
	 * @param UuidInterface          $userId      ID of the user initiating this change.
	 * @param UuidInterface|null     $processId   ID of the process this event belongs to.
	 * @param UuidInterface|null     $id          Optional ID for the event.
	 * @param DateTimeInterface|null $timestamp   Optional timestamp for the event (default now).
	 */
	public function __construct(
		public readonly string $handler,
		public readonly string $handlerKey,
		public readonly string $displayName,
		#[MapType('mixed')] public readonly array $details,
		public readonly UuidInterface $userId,
		public readonly ?UuidInterface $processId = null,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
	) {
		$this->setIdAndTime($id, $timestamp);
	}

	/**
	 * Get the Connection object described by this event.
	 *
	 * @return Connection
	 */
	public function getConnectionObject(): Connection {
		return new Connection(
			userId: $this->userId,
			handler: $this->handler,
			handlerKey: $this->handlerKey,
			displayName: $this->displayName,
			details: $this->details,
		);
	}

	/**
	 * Aggregate ID is not used
	 *
	 * @var null
	 */
	#[Field(exclude: true)]
	public null $aggregateId { get => null; }

	/**
	 * Entity ID is created from handler and handler key.
	 *
	 * @var null
	 */
	#[Field(exclude: true)]
	public UuidInterface $entityId {
		get => Connection::buildId(handler: $this->handler, handlerKey: $this->handlerKey);
	}
}
