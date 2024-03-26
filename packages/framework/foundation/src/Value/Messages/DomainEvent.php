<?php

namespace Smolblog\Framework\Foundation\Value\Messages;

use Smolblog\Framework\Foundation\Value;
use Smolblog\Framework\Foundation\Value\Fields\DateTimeField;
use Smolblog\Framework\Foundation\Value\Fields\Identifier;
use Smolblog\Framework\Foundation\Value\Traits\Entity;
use Smolblog\Framework\Foundation\Value\Traits\EntityKit;
use Smolblog\Framework\Foundation\Value\Traits\Message;
use Smolblog\Framework\Foundation\Value\Traits\MessageKit;
use Smolblog\Framework\Foundation\Value\Traits\MessageMetadata;

/**
 * A domain event represents a change in state in the system.
 */
readonly class DomainEvent extends Value implements Entity, Message {
	use MessageKit;
	use EntityKit;

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
		Identifier $id,
		public DateTimeField $timestamp,
		public Identifier $userId,
		public ?Identifier $aggregateId = null,
		public ?Identifier $entityId = null,
	) {
		$this->meta = new MessageMetadata();
		$this->id = $id;
	}

	/**
	 * Serialize the object.
	 *
	 * Adds the `type` property to the serialized object.
	 *
	 * @return array
	 */
	public function toArray(): array {
		$base = parent::toArray();
		$base['type'] = get_class($this);
		return $base;
	}

	/**
	 * Deserialize the object.
	 *
	 * Removes the `type` property from the serialized object.
	 *
	 * @param array $data Serialized object.
	 * @return static
	 */
	public static function fromArray(array $data): static {
		unset($data['type']);
		return parent::fromArray($data);
	}

	/**
	 * Deserialize the object using the type property.
	 *
	 * @param array $data Serialized object.
	 * @return static
	 */
	public static function deserializeWithType(array $data): static {
		$type = $data['type'] ?? null;

		if (isset($type) && class_exists($type) && is_subclass_of($type, self::class)) {
			unset($data['type']);
			return $type::fromArray($data);
		}

		return UnknownDomainEvent::fromArray($data);
	}
}
