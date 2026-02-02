<?php

namespace Smolblog\Core\Site\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Cavatappi\Foundation\Validation\AtLeastOneOf;
use Cavatappi\Foundation\Validation\Validated;
use Cavatappi\Foundation\Validation\ValidatedKit;
use Crell\Serde\Attributes\Field;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Foundation\Exceptions\InvalidValueProperties;

/**
 * Indicates that the details of a Site have been updated with new values.
 */
#[AtLeastOneOf('displayName', 'description', 'pictureId')]
class SiteDetailsUpdated implements DomainEvent, Validated {
	use DomainEventKit;
	use ValidatedKit;

	/**
	 * Create the event.
	 *
	 * @throws InvalidValueProperties When no updated attributes are provided.
	 *
	 * @param UuidInterface          $userId      User making the change.
	 * @param UuidInterface          $aggregateId Site being changed.
	 * @param string|null            $displayName New title for the site; null for no change.
	 * @param string|null            $description New description for the site; null for no change.
	 * @param UuidInterface|null     $pictureId   New picture for the site; null for no change.
	 * @param UuidInterface|null     $id          ID of the event.
	 * @param DateTimeInterface|null $timestamp   Timestamp of the event.
	 * @param UuidInterface|null     $processId   Optional process that created this event.
	 */
	public function __construct(
		public readonly UuidInterface $userId,
		public readonly UuidInterface $aggregateId,
		public readonly ?string $displayName = null,
		public readonly ?string $description = null,
		public readonly ?UuidInterface $pictureId = null,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		public readonly ?UuidInterface $processId = null,
	) {
		$this->setIdAndTime($id, $timestamp);
		$this->validate();
	}

	/**
	 * Entity ID is not used
	 *
	 * @var null
	 */
	#[Field(exclude: true)]
	public null $entityId { get => null; }
}
