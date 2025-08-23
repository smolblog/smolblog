<?php

namespace Smolblog\Foundation\v2\Value\Traits;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

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
	 * UUID of the user/entity making the change.
	 *
	 * If an event is happening without user interaction, use a dedicated system user identifier or the nil UUID.
	 *
	 * @var UuidInterface
	 */
	public UuidInterface $userId { get; }

	/**
	 * Type for this event.
	 *
	 * Typically the fully-qualified class name, as that's least likely to cause collisions.
	 *
	 * @var string
	 */
	public string $type { get; }

	/**
	 * Optional UUID for the "entity" this Event refers to.
	 *
	 * For example, in a content management system, it would be the individual piece of content being edited.
	 *
	 * @var UuidInterface|null
	 */
	public ?UuidInterface $entityId { get; }

	/**
	 * Optional UUID for the "aggregate" this Event refers to.
	 *
	 * If the entity belongs to a larger identifiable entity, then this would be its ID.
	 *
	 * @var UuidInterface|null
	 */
	public ?UuidInterface $aggregateId { get; }

	/**
	 * Optional UUID for the process that created this Event.
	 *
	 * This is in place to link subsequent events together. An asynchronous process could create at least two events:
	 * one denoting the start of the process and one recording the result. This identifier links the two events together
	 * so it is clear which result belongs to which process.
	 *
	 * @var UuidInterface|null
	 */
	public ?UuidInterface $processId { get; }
}
