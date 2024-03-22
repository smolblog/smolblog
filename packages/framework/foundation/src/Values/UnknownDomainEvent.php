<?php

namespace Smolblog\Framework\Foundation\Values;

use Smolblog\Framework\Foundation\RuntimeValueKit;

/**
 * A domain event that is not known to the system.
 *
 * Used when deserializing an event where class_exists($type) returns false. This allows processing of events that
 * no longer have a class definition, or at least prevents throwing an error in those cases.
 */
readonly class UnknownDomainEvent extends DomainEvent {
	/**
	 * Construct the event
	 *
	 * @param Identifier      $id          ID of the event.
	 * @param DateTime        $timestamp   Timestamp of the event.
	 * @param Identifier      $userId      ID of the user that created this event.
	 * @param Identifier|null $aggregateId Optional ID of the aggregate that this event belongs to.
	 * @param Identifier|null $entityId    Optional ID of the entity that this event belongs to.
	 * @param array           $props       Additional properties.
	 */
	public function __construct(
		Identifier $id,
		public DateTime $timestamp,
		public Identifier $userId,
		public ?Identifier $aggregateId = null,
		public ?Identifier $entityId = null,
		public array $props = [],
	) {
		parent::__construct($id, $timestamp, $userId, $aggregateId, $entityId);
	}

	public static function deserialize(array $data): static {
		if (!isset($data['props'])) {
			// remove all unknown fields and put them in $data['props']
		}

		return parent::deserialize($data);
	}
}
