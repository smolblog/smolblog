<?php

namespace Smolblog\Core\Connection\Events;

use Smolblog\Core\Connection\Entities\Connection;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Foundation\Value\Traits\ArrayType;

/**
 * Indicates a Connection has been formed or re-formed between a user account and an external handler.
 */
readonly class ConnectionEstablished extends DomainEvent {
	/**
	 * Create the Event
	 *
	 * @param string             $handler     Key for the handler this connection is for.
	 * @param string             $handlerKey  Unique identifier for this connection for this handler.
	 * @param string             $displayName Human-readable name for this connection.
	 * @param array              $details     Additional information needed to connect to this handler.
	 * @param Identifier         $userId      ID of the user initiating this change.
	 * @param Identifier|null    $entityId    ID of the connection this event belongs to.
	 * @param Identifier|null    $id          Optional ID for the event.
	 * @param DateTimeField|null $timestamp   Optional timestamp for the event (default now).
	 */
	public function __construct(
		public readonly string $handler,
		public readonly string $handlerKey,
		public readonly string $displayName,
		#[ArrayType(ArrayType::NO_TYPE, isMap: true)] public readonly array $details,
		Identifier $userId,
		?Identifier $entityId = null,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
	) {
		$calculatedId = $entityId ?? Connection::buildId(handler: $handler, handlerKey: $handlerKey);
		parent::__construct(entityId: $calculatedId, userId: $userId, id: $id, timestamp: $timestamp);
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
}
