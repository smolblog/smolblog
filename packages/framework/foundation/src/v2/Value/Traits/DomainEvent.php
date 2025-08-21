<?php

namespace Smolblog\Foundation\v2\Value\Traits;

use DateTimeInterface;
use Smolblog\Foundation\Value\Fields\Identifier;

/**
 * Represents a Thing That Has Happened.
 *
 * The intent is to serialize and persist the event itself as the canonical store for the application. If this doesn't
 * match your use case, this interface is not recommended.
 */
interface DomainEvent extends Entity {
	/**
	 * Date and time that this Event occurred.
	 *
	 * @var DateTimeInterface
	 */
	public DateTimeInterface $timestamp { get; }

	/**
	 * Identifier of the user/entity making the change.
	 *
	 * If an event is happening without user interaction, use a dedicated system user identifier or Identifier::Nil().
	 *
	 * @var Identifier
	 */
	public Identifier $userId { get; }

	/**
	 * Type for this event.
	 *
	 * Typically the fully-qualified class name, as that's least likely to cause collisions.
	 *
	 * @var string
	 */
	public string $type { get; }

	/**
	 * Optional Identifier for the "entity" this Event refers to.
	 *
	 * For example, in a content management system, it would be the individual piece of content being edited.
	 *
	 * @var Identifier|null
	 */
	public ?Identifier $entityId { get; }

	/**
	 * Optional Identifier for the "aggregate" this Event refers to.
	 *
	 * If the entity belongs to a larger identifiable entity, then this would be its ID.
	 *
	 * @var Identifier|null
	 */
	public ?Identifier $aggregateId { get; }

	/**
	 * Optional Identifier for the process that created this Event.
	 *
	 * This is in place to link subsequent events together. An asynchronous process could create at least two events:
	 * one denoting the start of the process and one recording the result. This identifier links the two events together
	 * so it is clear which result belongs to which process.
	 *
	 * @var Identifier|null
	 */
	public ?Identifier $processId { get; }
}
