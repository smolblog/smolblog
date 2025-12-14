<?php

namespace Smolblog\Core\Channel\Events;

use ReflectionClass;
use ReflectionProperty;
use Smolblog\Core\Channel\Entities\Channel;
use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;
use Smolblog\Foundation\Value\ValueProperty;

/**
 * Indicates a Channel has been created.
 */
readonly class ChannelSaved extends DomainEvent {
	/**
	 * Create the event.
	 *
	 * @param Channel            $channel   Channel object being saved.
	 * @param Identifier         $userId    User creating the channel.
	 * @param Identifier|null    $entityId  Channel ID; will be auto-generated.
	 * @param Identifier|null    $id        Optional ID for the event.
	 * @param DateTimeField|null $timestamp Optional timestamp for the event.
	 */
	public function __construct(
		public Channel $channel,
		Identifier $userId,
		?Identifier $entityId = null,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
	) {
		parent::__construct(
			entityId: $entityId ?? $this->channel->getId(),
			userId: $userId,
			id: $id,
			timestamp: $timestamp
		);
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
