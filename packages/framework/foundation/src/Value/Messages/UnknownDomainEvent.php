<?php

namespace Smolblog\Foundation\Value\Messages;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Throwable;

/**
 * A domain event that is not known to the system.
 *
 * Used when deserializing an event where class_exists($type) returns false. This allows processing of events that
 * no longer have a class definition, or at least prevents throwing an error in those cases.
 */
final readonly class UnknownDomainEvent extends DomainEvent {
	/**
	 * Construct the event
	 *
	 * @param Identifier      $id          ID of the event.
	 * @param DateTimeField   $timestamp   Timestamp of the event.
	 * @param Identifier      $userId      ID of the user that created this event.
	 * @param Identifier|null $aggregateId Optional ID of the aggregate that this event belongs to.
	 * @param Identifier|null $entityId    Optional ID of the entity that this event belongs to.
	 * @param array           $props       Additional properties.
	 */
	public function __construct(
		Identifier $id,
		DateTimeField $timestamp,
		Identifier $userId,
		?Identifier $aggregateId = null,
		?Identifier $entityId = null,
		public array $props = [],
	) {
		parent::__construct($id, $timestamp, $userId, $aggregateId, $entityId);
	}

	/**
	 * Deserialize the object. Any unknown fields are put in the `props` array.
	 *
	 * @throws InvalidValueProperties Thrown if the object could not be deserialized.
	 *
	 * @param array $data Serialized object.
	 * @return static
	 */
	public static function deserializeValue(array $data): static {
		if (isset($data['props'])) {
			return self::baseDeserialize($data);
		}

		$modified = [
			'id' => $data['id'] ?? null,
			'timestamp' => $data['timestamp'] ?? null,
			'userId' => $data['userId'] ?? null,
			'aggregateId' => $data['aggregateId'] ?? null,
			'entityId' => $data['entityId'] ?? null,
		];
		$modified['props'] = array_diff_key($data, $modified);

		try {
			return self::baseDeserialize($modified);
		} catch (Throwable $e) {
			throw new InvalidValueProperties(
				message: 'Could not deserialize to a DomainEvent',
				previous: $e
			);
		}
	}
}
