<?php

namespace Smolblog\Foundation\Value\Messages;

use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Traits\ArrayType;
use Smolblog\Foundation\Value\Traits\SerializableSupertypeBackupKit;

/**
 * A domain event that is not known to the system.
 *
 * Used when deserializing an event where class_exists($type) returns false. This allows processing of events that
 * no longer have a class definition, or at least prevents throwing an error in those cases.
 */
final readonly class UnknownDomainEvent extends DomainEvent {
	use SerializableSupertypeBackupKit;

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
		#[ArrayType(ArrayType::NO_TYPE, isMap: true)] public array $props = [],
	) {
		parent::__construct($userId, $id, $timestamp, $aggregateId, $entityId);
	}

	/**
	 * Note that extra properties go in the $props field.
	 *
	 * @return string
	 */
	private static function extraPropsField(): string {
		return 'props';
	}
}
