<?php

namespace Smolblog\Foundation\Value\Messages;

use DateTimeImmutable;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\EntityKit;
use Smolblog\Foundation\Value\Traits\SerializableSupertypeKit;
use Smolblog\Foundation\Value\Traits\SerializableValue;

/**
 * A domain event represents a change in state in the system.
 */
readonly class DomainEvent extends Value implements Entity, SerializableValue {
	use SerializableSupertypeKit;
	use EntityKit;

	/**
	 * Deserialize unknown event types to UnknownDomainEvent.
	 *
	 * @return string
	 */
	public static function getFallbackClass(): string {
		return UnknownDomainEvent::class;
	}

	/**
	 * Timestamp of the event.
	 *
	 * @var DateTimeField
	 */
	public DateTimeField $timestamp;

	/**
	 * Construct the event
	 *
	 * @param Identifier         $userId      ID of the user that created this event.
	 * @param Identifier|null    $id          ID of the event.
	 * @param DateTimeField|null $timestamp   Timestamp of the event.
	 * @param Identifier|null    $aggregateId Optional ID of the aggregate that this event belongs to.
	 * @param Identifier|null    $entityId    Optional ID of the entity that this event belongs to.
	 * @param Identifier|null    $processId   Optional ID of a process (series of events) this event belongs to.
	 */
	public function __construct(
		public Identifier $userId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
		public ?Identifier $aggregateId = null,
		public ?Identifier $entityId = null,
		public ?Identifier $processId = null,
	) {
		// Applying the default values here allows subclasses to pass a null value to this constructor.
		$this->timestamp = $timestamp ?? new DateTimeField();
		$this->id = $id ?? new DateIdentifier($this->timestamp);
	}
}
