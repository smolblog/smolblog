<?php

namespace Smolblog\Core\Connector\Events;

use Smolblog\Core\Connector\Entities\Connection;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * Indicates a Connection has been formed or re-formed between a user account and an external provider.
 */
readonly class ConnectionEstablished extends DomainEvent {
	/**
	 * Create the Event
	 *
	 * @param string             $provider    Key for the provider this connection is for.
	 * @param string             $providerKey Unique identifier for this connection for this provider.
	 * @param string             $displayName Human-readable name for this connection.
	 * @param array              $details     Additional information needed to connect to this provider.
	 * @param Identifier         $userId      ID of the user initiating this change.
	 * @param Identifier|null    $entityId    ID of the connection this event belongs to.
	 * @param Identifier|null    $id          Optional ID for the event.
	 * @param DateTimeField|null $timestamp   Optional timestamp for the event (default now).
	 */
	public function __construct(
		public readonly string $provider,
		public readonly string $providerKey,
		public readonly string $displayName,
		public readonly array $details,
		Identifier $userId,
		?Identifier $entityId = null,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
	) {
		$calculatedId = $entityId ?? Connection::buildId(provider: $provider, providerKey: $providerKey);
		parent::__construct(entityId: $calculatedId, userId: $userId, id: $id, timestamp: $timestamp);
	}

	/**
	 * For subclasses to provide any additional fields in a serialized array format.
	 *
	 * In this format so that the additional fields can be type-checked by the subclasses but still serialized and
	 * stored in a standard format.
	 *
	 * @return array
	 */
	public function getPayload(): array {
		return [
			'provider' => $this->provider,
			'providerKey' => $this->providerKey,
			'displayName' => $this->displayName,
			'details' => $this->details,
		];
	}
}
