<?php

namespace Smolblog\Core\Channel\Events;

use ReflectionClass;
use ReflectionProperty;
use Smolblog\Foundation\Value\Fields\{DateTimeField, Identifier};
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Foundation\Value\ValueProperty;

/**
 * Indicates a Channel is no longer active and has been deleted.
 */
readonly class ChannelDeleted extends DomainEvent {
	/**
	 * Construct the event.
	 *
	 * @param Identifier         $entityId  ID of the channel being deleted.
	 * @param Identifier         $userId    ID of the user initiating this change.
	 * @param Identifier|null    $id        Optional ID for the event.
	 * @param DateTimeField|null $timestamp Optional timestamp for the event (default now).
	 */
	public function __construct(
		Identifier $entityId,
		Identifier $userId,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
	) {
		parent::__construct(entityId: $entityId, userId: $userId, id: $id, timestamp: $timestamp);
	}

	/**
	 * Remove 'aggregateId' from (de)serialization.
	 *
	 * @param ReflectionProperty $prop  ReflectionProperty for the property being evaluated.
	 * @param ReflectionClass    $class ReflectionClass for this class.
	 * @return ValueProperty|null
	 */
	protected static function getPropertyInfo(ReflectionProperty $prop, ReflectionClass $class): ?ValueProperty {
		return ($prop->getName() === 'aggregateId') ? null : parent::getPropertyInfo(prop: $prop, class: $class);
	}
}
