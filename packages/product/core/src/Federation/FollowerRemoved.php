<?php

namespace Smolblog\Core\Federation;

use Smolblog\Foundation\Value\Fields\DateTimeField;
use Smolblog\Foundation\Value\Fields\Identifier;
use Smolblog\Foundation\Value\Messages\DomainEvent;

/**
 * The blog has lost a follower, either through removal or deletion. How this looks depends on the individual module.
 */
readonly class FollowerRemoved extends DomainEvent {
	/**
	 * Construct the event.
	 *
	 * If an action is being taken by the system and not a user, use Smolblog\Core\User\User::internalSystemUser().
	 *
	 * @param Identifier             $aggregateId      Site this event belongs to.
	 * @param Identifier             $userId      User instigating the event.
	 * @param string                 $provider    Service the Follower belongs to.
	 * @param string                 $providerKey ID of the Follower being removed.
	 * @param Identifier|null        $id          ID of the event.
	 * @param DateTimeField|null $timestamp   Time of the event.
	 */
	public function __construct(
		Identifier $aggregateId,
		Identifier $userId,
		public readonly string $provider,
		public readonly string $providerKey,
		?Identifier $id = null,
		?DateTimeField $timestamp = null,
	) {
		parent::__construct(
			aggregateId: $aggregateId,
			userId: $userId,
			id: $id,
			timestamp: $timestamp
		);
	}
}
