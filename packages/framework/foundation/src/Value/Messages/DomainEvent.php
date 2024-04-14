<?php

namespace Smolblog\Foundation\Value\Messages;

use PHPUnit\Framework\TestSize\Unknown;
use Smolblog\Foundation\Value;
use Smolblog\Foundation\Value\Fields\DateIdentifier;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Traits\Entity;
use Smolblog\Foundation\Value\Traits\EntityKit;
use Smolblog\Foundation\Value\Traits\Message;
use Smolblog\Foundation\Value\Traits\MessageKit;
use Smolblog\Foundation\Value\Traits\MessageMetadata;
use Smolblog\Foundation\Value\Traits\SerializableSupertypeKit;
use Smolblog\Foundation\Value\Traits\SerializableValue;

/**
 * A domain event represents a change in state in the system.
 */
readonly class DomainEvent extends Value implements Entity, Message, SerializableValue {
	use SerializableSupertypeKit;
	use MessageKit;
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
	 * @param Identifier      $id          ID of the event.
	 * @param DateTimeField   $timestamp   Timestamp of the event.
	 * @param Identifier      $userId      ID of the user that created this event.
	 * @param Identifier|null $aggregateId Optional ID of the aggregate that this event belongs to.
	 * @param Identifier|null $entityId    Optional ID of the entity that this event belongs to.
	 */
	public function __construct(
		public Identifier $userId,
		Identifier $id = null,
		DateTimeField $timestamp = null,
		public ?Identifier $aggregateId = null,
		public ?Identifier $entityId = null,
	) {
		$this->meta = new MessageMetadata();
		$this->id = $id ?? new DateIdentifier();
		$this->timestamp = $timestamp ?? new DateTimeField();
	}
}
