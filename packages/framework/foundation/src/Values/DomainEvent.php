<?php

namespace Smolblog\Framework\Foundation\Values;

use Smolblog\Framework\Foundation\Message;
use Smolblog\Framework\Foundation\MessageKit;

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
		parent::__construct($id);
	}

	public static function deserializeWithType(array $data): static {
		if (class_exists($data['type'])) {
			return $data['type']::deserialize($data);
		}

		return UnknownDomainEvent::deserialize($data);
	}
}
