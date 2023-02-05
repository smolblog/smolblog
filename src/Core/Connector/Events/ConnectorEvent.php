<?php

namespace Smolblog\Core\Connector\Events;

use DateTimeInterface;
use Smolblog\Framework\Messages\Event;
use Smolblog\Framework\Messages\PayloadKit;
use Smolblog\Framework\Objects\Identifier;

/**
 * Base event for Connector-related events.
 */
abstract class ConnectorEvent extends Event {
	use PayloadKit;

	/**
	 * Create the Event
	 *
	 * @param Identifier             $connectionId ID of the connection this event belongs to.
	 * @param Identifier             $userId       ID of the user initiating this change.
	 * @param Identifier|null        $id           Optional ID for the event.
	 * @param DateTimeInterface|null $timestamp    Optional timestamp for the event (default now).
	 */
	public function __construct(
		public readonly Identifier $connectionId,
		public readonly Identifier $userId,
		Identifier $id = null,
		DateTimeInterface $timestamp = null,
	) {
		parent::__construct(id: $id, timestamp: $timestamp);
	}

	/**
	 * Get the properties defined on this class as a serialized array.
	 *
	 * @return array
	 */
	private function getStandardProperties(): array {
		return [
			'connectionId' => $this->connectionId->toString(),
			'userId' => $this->userId->toString(),
		];
	}

	/**
	 * Unserialize the standard properties for these events
	 *
	 * @param array $properties Array of serialized properties.
	 * @return array
	 */
	protected static function standardPropertiesFromArray(array $properties): array {
		return array_map(fn($str) => Identifier::fromString($str), $properties);
	}
}
