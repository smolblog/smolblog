<?php

namespace Smolblog\Core\Channel\Events;

use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\DomainEvent\DomainEventKit;
use Crell\Serde\Attributes\Field;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use Smolblog\Core\Channel\Entities\Channel;

/**
 * Indicates a Channel has been created.
 */
class ChannelSaved implements DomainEvent {
	use DomainEventKit;

	public readonly UuidInterface $entityId;

	/**
	 * Create the event.
	 *
	 * @param Channel                $channel   Channel object being saved.
	 * @param UuidInterface          $userId    User creating the channel.
	 * @param UuidInterface|null     $entityId  Channel ID; will be auto-generated.
	 * @param UuidInterface|null     $processId Optional ID for overall process.
	 * @param UuidInterface|null     $id        Optional ID for the event.
	 * @param DateTimeInterface|null $timestamp Optional timestamp for the event.
	 */
	public function __construct(
		public readonly Channel $channel,
		public readonly UuidInterface $userId,
		?UuidInterface $entityId = null,
		public readonly ?UuidInterface $processId = null,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
	) {
		$this->entityId = $entityId ?? $this->channel->id;
		$this->setIdAndTime($id, $timestamp);
	}

	#[Field(exclude: true)]
	public null $aggregateId { get => null; }
}
