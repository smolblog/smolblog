<?php

namespace Smolblog\Framework\Foundation\Messages;

use Smolblog\Framework\Foundation\Message;
use Smolblog\Framework\Foundation\MessageKit;
use Smolblog\Framework\Foundation\MessageMetadata;
use Smolblog\Framework\Foundation\Values\Entity;
use Smolblog\Framework\Foundation\Values\Identifier;
use Smolblog\Framework\Foundation\Values\DateTime;

/**
 * A domain event represents a change in state in the system.
 */
abstract readonly class DomainEvent extends Entity implements Message {
	use MessageKit;

	/**
	 * Construct the event
	 *
	 * @param Identifier      $id          ID of the event.
	 * @param DateTime        $timestamp   Timestamp of the event.
	 * @param Identifier      $userId      ID of the user that created this event.
	 * @param Identifier|null $aggregateId Optional ID of the aggregate that this event belongs to.
	 * @param Identifier|null $entityId    Optional ID of the entity that this event belongs to.
	 */
	public function __construct(
		Identifier $id,
		public DateTime $timestamp,
		public Identifier $userId,
		public ?Identifier $aggregateId = null,
		public ?Identifier $entityId = null,
	) {
		$this->meta = new MessageMetadata();
		parent::__construct($id);
	}

	/**
	 * Serialize the object.
	 *
	 * Adds the `type` property to the serialized object.
	 *
	 * @return array
	 */
	public function serialize(): array {
		$base = parent::serialize();
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
	public static function deserialize(array $data): static {
		unset($data['type']);
		return parent::deserialize($data);
	}

	/**
	 * Deserialize the object using the type property.
	 *
	 * @param array $data Serialized object.
	 * @return static
	 */
	public static function deserializeWithType(array $data): static {
		$type = $data['type'];
		unset($data['type']);

		if (class_exists($type)) {
			return $type::deserialize($data);
		}

		return UnknownDomainEvent::deserialize($data);
	}
}
