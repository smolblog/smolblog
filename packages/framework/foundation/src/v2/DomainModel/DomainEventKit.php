<?php

namespace Smolblog\Foundation\v2\DomainModel;

/**
 * Useful defaults for DomainEvents.
 *
 * Provides null values for entityId, aggregateId, and processId. Provides the class name for type.
 */
trait DomainEventKit {
	/**
	 * Provide the fully-qualified class name as the Event type.
	 *
	 * @var class-string<static>
	 */
	public string $type { get { return static::class; } }

	/**
	 * No entityId by default.
	 *
	 * @var null
	 */
	public null $entityId { get { return null; } }

	/**
	 * No aggregateId by default.
	 *
	 * @var null
	 */
	public null $aggregateId { get { return null; } }

	/**
	 * No processId by default.
	 *
	 * @var null
	 */
	public null $processId { get { return null; } }
}
