<?php

namespace Smolblog\Foundation\v2\Value\Traits;

use Ramsey\Uuid\UuidInterface;

trait DomainEventKit {
	public string $type { get { return static::class; } }
	public array $implements { get {
		return [
			...class_parents(static::class) ?: [],
			...class_implements(static::class) ?: [],
		];
	} }
	public ?UuidInterface $entityId { get { return null; } }
	public ?UuidInterface $aggregateId { get { return null; } }
	public ?UuidInterface $processId { get { return null; } }
}
