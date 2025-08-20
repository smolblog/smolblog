<?php

namespace Smolblog\Foundation\v2\Value\Traits;

use DateTimeInterface;
use Smolblog\Foundation\Value\Fields\Identifier;

interface DomainEvent extends Entity {
	public DateTimeInterface $timestamp { get; }
	public Identifier $userId { get; }
	public string $type { get; }
	public array $implements { get; }
	public ?Identifier $entityId { get; }
	public ?Identifier $aggregateId { get; }
	public ?Identifier $processId { get; }
}
