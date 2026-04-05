<?php

namespace Smolblog\Core\User;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Crell\Serde\Attributes\Field;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class UserGrantedSudo implements DomainEvent {
	use DomainEventKit;

	public function __construct(
		public readonly UuidInterface $userId,
		public readonly UuidInterface $entityId,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		public readonly ?UuidInterface $processId = null,
	) {
		$this->setIdAndTime($id, $timestamp);
	}

	#[Field(exclude: true)]
	public null $aggregateId { get => null; }
}
